<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;

class BillingController extends Controller
{
    /**
     * Initiate a Stripe Checkout session for the given plan.
     */
    public function checkout(Request $request, Plan $plan): RedirectResponse
    {
        $request->validate([
            'billing_cycle' => ['required', 'in:monthly,yearly,lifetime'],
        ]);

        $billingCycle = $request->input('billing_cycle');

        // Free plan: assign directly without Stripe.
        if ($plan->price_monthly == 0 && $billingCycle === 'monthly') {
            $this->assignFreePlan($request->user(), $plan);

            return redirect()->route('subscription')->with('success', 'Plan activé avec succès.');
        }

        $stripePriceId = match ($billingCycle) {
            'monthly' => $plan->stripe_monthly_price_id,
            'yearly' => $plan->stripe_yearly_price_id,
            'lifetime' => $plan->stripe_lifetime_price_id,
        };

        if (! $stripePriceId) {
            return redirect()->route('subscription')->with('error', 'Ce plan n\'est pas encore disponible pour le paiement en ligne. Contactez-nous.');
        }

        $checkoutOptions = [
            'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscription'),
            'metadata' => [
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
            ],
        ];

        if ($billingCycle === 'lifetime') {
            return $request->user()->checkout($stripePriceId, $checkoutOptions);
        }

        // Recurring subscription via Stripe Checkout.
        return $request->user()->newSubscription('default', $stripePriceId)
            ->checkout($checkoutOptions);
    }

    /**
     * Handle the success redirect after Stripe Checkout.
     */
    public function success(Request $request): View|RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('subscription');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        return view('billing.success', ['session' => $session]);
    }

    /**
     * Redirect the user to the Stripe billing portal.
     */
    public function portal(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(route('subscription'));
    }

    /**
     * Assign a free plan directly without Stripe.
     */
    private function assignFreePlan(\App\Models\User $user, Plan $plan): void
    {
        // Cancel any existing active subscription.
        $user->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'type' => 'free',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => null,
        ]);
    }
}
