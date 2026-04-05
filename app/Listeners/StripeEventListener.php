<?php

namespace App\Listeners;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Handle received Stripe webhook events.
     */
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;

        match ($payload['type'] ?? null) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($payload),
            'customer.subscription.updated' => $this->handleCustomerSubscriptionUpdated($payload),
            'customer.subscription.deleted' => $this->handleCustomerSubscriptionDeleted($payload),
            default => null,
        };
    }

    /**
     * Handle checkout.session.completed — creates or updates our custom Subscription.
     */
    private function handleCheckoutSessionCompleted(array $payload): void
    {
        $session = $payload['data']['object'];
        $metadata = $session['metadata'] ?? [];

        $planId = $metadata['plan_id'] ?? null;
        $billingCycle = $metadata['billing_cycle'] ?? 'monthly';
        $customerId = $session['customer'] ?? null;

        if (! $planId || ! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();
        $plan = Plan::find($planId);

        if (! $user || ! $plan) {
            return;
        }

        // Cancel any existing active subscription.
        $user->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        $endsAt = match ($billingCycle) {
            'yearly' => now()->addYear(),
            'lifetime' => null,
            default => now()->addMonth(),
        };

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'type' => $billingCycle,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => $endsAt,
        ]);
    }

    /**
     * Handle customer.subscription.updated — sync renewal date.
     */
    private function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $stripeSubscription = $payload['data']['object'];
        $customerId = $stripeSubscription['customer'] ?? null;
        $currentPeriodEnd = $stripeSubscription['current_period_end'] ?? null;
        $status = $stripeSubscription['status'] ?? null;

        if (! $customerId) {
            return;
        }

        $user = User::where('stripe_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $subscription = $user->subscriptions()
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->latest()
            ->first();

        if (! $subscription) {
            return;
        }

        $updates = [];

        if ($currentPeriodEnd) {
            $updates['ends_at'] = Carbon::createFromTimestamp($currentPeriodEnd);
        }

        if ($status === 'canceled') {
            $updates['status'] = 'cancelled';
            $updates['cancelled_at'] = now();
        }

        if (! empty($updates)) {
            $subscription->update($updates);
        }
    }

    /**
     * Handle customer.subscription.deleted — cancel our custom Subscription.
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

        $user->subscriptions()
            ->where('status', 'active')
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
    }
}
