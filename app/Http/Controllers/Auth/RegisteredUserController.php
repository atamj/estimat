<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateDemoWorkspaceData;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $plan = $request->query('plan');
        $billingCycle = $request->query('billing_cycle');

        return view('auth.register', [
            'selectedPlanSlug' => is_string($plan) && $plan !== '' ? $plan : null,
            'selectedBillingCycle' => is_string($billingCycle) && $billingCycle !== '' ? $billingCycle : null,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => ['nullable', 'string', 'max:255'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly,lifetime'],
        ]);

        $freePlan = Plan::where('slug', 'free')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'plan_id' => $freePlan?->id,
        ]);

        CreateDemoWorkspaceData::run($user);

        event(new Registered($user));

        Auth::login($user);

        $redirectTo = route('estimations.index', absolute: false);

        $planSlug = $validated['plan'] ?? null;
        $billingCycle = $validated['billing_cycle'] ?? null;

        if (is_string($planSlug) && is_string($billingCycle)) {
            $selectedPlan = Plan::query()
                ->where('slug', $planSlug)
                ->where('is_active', true)
                ->first();

            if ($selectedPlan && $this->checkoutIntentIsValid($selectedPlan, $billingCycle)) {
                $request->session()->flash('pending_checkout', [
                    'plan_id' => $selectedPlan->id,
                    'billing_cycle' => $billingCycle,
                ]);
                $redirectTo = route('subscription', absolute: false);
            }
        }

        return redirect($redirectTo);
    }

    /**
     * Whether the user should be sent to Stripe Checkout after registration.
     */
    private function checkoutIntentIsValid(Plan $plan, string $billingCycle): bool
    {
        if ($plan->slug === 'free') {
            return false;
        }

        if ($plan->slug === 'pioneer' && $billingCycle !== 'lifetime') {
            return false;
        }

        if ($plan->slug === 'pro' && $billingCycle === 'lifetime') {
            return false;
        }

        $stripePriceId = match ($billingCycle) {
            'monthly' => $plan->stripe_monthly_price_id,
            'yearly' => $plan->stripe_yearly_price_id,
            'lifetime' => $plan->stripe_lifetime_price_id,
        };

        return $stripePriceId !== null && $stripePriceId !== '';
    }
}
