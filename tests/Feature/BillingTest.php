<?php

namespace Tests\Feature;

use App\Listeners\StripeEventListener;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\TestCase;

class BillingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a plan for use in tests.
     *
     * @param  array<string, mixed>  $overrides
     */
    private function createPlan(array $overrides = []): Plan
    {
        return Plan::create(array_merge([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Plan Pro',
            'price_monthly' => 19.00,
            'price_yearly' => 149.00,
            'price_lifetime' => null,
            'max_estimations' => -1,
            'max_blocks' => -1,
            'has_white_label_pdf' => true,
            'has_translation_module' => true,
            'is_active' => true,
        ], $overrides));
    }

    public function test_subscription_page_is_accessible_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('subscription'))->assertOk();
    }

    public function test_subscription_page_redirects_guests(): void
    {
        $this->get(route('subscription'))->assertRedirect(route('login'));
    }

    public function test_checkout_free_plan_assigns_plan_directly(): void
    {
        $user = User::factory()->create();
        $freePlan = $this->createPlan([
            'name' => 'Gratuit',
            'slug' => 'gratuit',
            'price_monthly' => 0,
            'price_yearly' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('billing.checkout', $freePlan), [
            'billing_cycle' => 'monthly',
        ]);

        $response->assertRedirect(route('subscription'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'type' => 'free',
            'status' => 'active',
        ]);
    }

    public function test_checkout_free_plan_cancels_existing_subscription(): void
    {
        $user = User::factory()->create();
        $existingPlan = $this->createPlan(['slug' => 'existing', 'price_monthly' => 9]);
        $freePlan = $this->createPlan([
            'name' => 'Gratuit',
            'slug' => 'gratuit',
            'price_monthly' => 0,
            'price_yearly' => 0,
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $existingPlan->id,
            'type' => 'monthly',
            'status' => 'active',
            'starts_at' => now()->subMonth(),
        ]);

        $this->actingAs($user)->post(route('billing.checkout', $freePlan), [
            'billing_cycle' => 'monthly',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $existingPlan->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'status' => 'active',
        ]);
    }

    public function test_checkout_paid_plan_without_stripe_price_id_returns_error(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPlan([
            'price_monthly' => 19.00,
            // No stripe_monthly_price_id set.
        ]);

        $response = $this->actingAs($user)->post(route('billing.checkout', $plan), [
            'billing_cycle' => 'monthly',
        ]);

        $response->assertRedirect(route('subscription'));
        $response->assertSessionHas('error');
    }

    public function test_checkout_requires_valid_billing_cycle(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPlan();

        $response = $this->actingAs($user)->post(route('billing.checkout', $plan), [
            'billing_cycle' => 'invalid',
        ]);

        $response->assertSessionHasErrors('billing_cycle');
    }

    public function test_billing_portal_requires_authentication(): void
    {
        $this->get(route('billing.portal'))->assertRedirect(route('login'));
    }

    public function test_stripe_webhook_checkout_session_completed_creates_subscription(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_test123']);
        $plan = $this->createPlan();

        $listener = new StripeEventListener;
        $listener->handle(new WebhookReceived([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'customer' => 'cus_test123',
                    'metadata' => [
                        'plan_id' => $plan->id,
                        'billing_cycle' => 'monthly',
                    ],
                ],
            ],
        ]));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'type' => 'monthly',
            'status' => 'active',
        ]);
    }

    public function test_stripe_webhook_subscription_deleted_cancels_subscription(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_cancel123']);
        $plan = $this->createPlan();

        Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'type' => 'monthly',
            'status' => 'active',
            'starts_at' => now(),
        ]);

        $listener = new StripeEventListener;
        $listener->handle(new WebhookReceived([
            'type' => 'customer.subscription.deleted',
            'data' => [
                'object' => [
                    'customer' => 'cus_cancel123',
                ],
            ],
        ]));

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_stripe_webhook_with_unknown_customer_does_not_throw(): void
    {
        $listener = new StripeEventListener;

        // Should not throw any exception.
        $listener->handle(new WebhookReceived([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'customer' => 'cus_nonexistent',
                    'metadata' => [
                        'plan_id' => 999,
                        'billing_cycle' => 'monthly',
                    ],
                ],
            ],
        ]));

        $this->assertTrue(true);
    }
}
