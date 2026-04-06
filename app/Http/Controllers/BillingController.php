<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
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
            return $request->user()->checkout($stripePriceId, $checkoutOptions)->redirect();
        }

        // Recurring subscription via Stripe Checkout.
        return $request->user()->newSubscription('default', $stripePriceId)
            ->checkout($checkoutOptions)
            ->redirect();
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
     * Resume a canceled Stripe subscription still within its grace period.
     */
    public function resume(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription('default');

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return redirect()->route('subscription')->with('error', 'Aucun abonnement à reprendre.');
        }

        $subscription->resume();

        return redirect()->route('subscription')->with('success', 'Votre abonnement a bien été repris.');
    }

    /**
     * Redirect the user to the Stripe billing portal.
     */
    public function portal(Request $request): RedirectResponse
    {
        return $request->user()->redirectToBillingPortal(route('subscription'));
    }

    /**
     * Assign the free plan.
     *
     * If the free plan has a Stripe price configured, we swap or create the Stripe
     * subscription so the user appears in Stripe and upgrades are handled cleanly.
     * Otherwise we cancel any active paid subscription at period end and update locally.
     */
    private function assignFreePlan(User $user, Plan $plan): void
    {
        if ($plan->stripe_monthly_price_id) {
            $existingSubscription = $user->subscription('default');

            if ($existingSubscription && $existingSubscription->valid()) {
                $existingSubscription->swap($plan->stripe_monthly_price_id);
            } else {
                $user->newSubscription('default', $plan->stripe_monthly_price_id)->create();
            }
        } else {
            foreach ($user->subscriptions as $subscription) {
                if ($subscription->active() && ! $subscription->onGracePeriod()) {
                    $subscription->cancel();
                }
            }
        }

        $user->update(['plan_id' => $plan->id]);
    }
}
