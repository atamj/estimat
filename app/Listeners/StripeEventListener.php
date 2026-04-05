<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\User;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Handle received Stripe webhook events (complements Cashier’s own handling).
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        match ($payload['type'] ?? null) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($payload),
            'customer.subscription.deleted' => $this->handleCustomerSubscriptionDeleted($payload),
            default => null,
        };
    }

    /**
     * One-time “lifetime” Checkout does not create a Cashier subscription row; store plan on the user.
     */
    private function handleCheckoutSessionCompleted(array $payload): void
    {
        $session = $payload['data']['object'];
        $metadata = $session['metadata'] ?? [];
        $billingCycle = $metadata['billing_cycle'] ?? 'monthly';

        if ($billingCycle !== 'lifetime') {
            return;
        }

        $planId = $metadata['plan_id'] ?? null;
        $customerId = $session['customer'] ?? null;

        if (! $planId || ! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();
        $plan = Plan::find($planId);

        if (! $user || ! $plan) {
            return;
        }

        foreach ($user->subscriptions as $subscription) {
            if ($subscription->valid()) {
                $subscription->cancelNow();
            }
        }

        $user->update(['plan_id' => $plan->id]);
    }

    /**
     * When the Stripe subscription ends, fall back to the free plan for app entitlements.
     */
    private function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $stripeSubscription = $payload['data']['object'];
        $customerId = $stripeSubscription['customer'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $user->refresh();

        if (! $user->subscription('default')?->valid()) {
            $freePlan = Plan::where('slug', 'free')->first();
            if ($freePlan) {
                $user->update(['plan_id' => $freePlan->id]);
            }
        }
    }
}
