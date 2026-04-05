<?php

namespace Tests\Feature;

use App\Listeners\StripeEventListener;
use App\Models\Plan;
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
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan_id' => $freePlan->id,
        ]);
    }

    public function test_checkout_free_plan_replaces_existing_plan_id(): void
    {
        $existingPlan = $this->createPlan(['slug' => 'existing', 'price_monthly' => 9]);
        $freePlan = $this->createPlan([
            'name' => 'Gratuit',
            'slug' => 'gratuit',
            'price_monthly' => 0,
            'price_yearly' => 0,
        ]);

        $user = User::factory()->create(['plan_id' => $existingPlan->id]);

        $this->actingAs($user)->post(route('billing.checkout', $freePlan), [
            'billing_cycle' => 'monthly',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan_id' => $freePlan->id,
        ]);
    }

    public function test_checkout_paid_plan_without_stripe_price_id_returns_error(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPlan([
            'price_monthly' => 19.00,
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

    public function test_stripe_webhook_checkout_session_completed_sets_lifetime_plan_for_user(): void
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
                        'billing_cycle' => 'lifetime',
                    ],
                ],
            ],
        ]));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan_id' => $plan->id,
        ]);
    }

    public function test_stripe_webhook_subscription_deleted_assigns_free_plan(): void
    {
        $freePlan = $this->createPlan([
            'name' => 'Free',
            'slug' => 'free',
            'price_monthly' => 0,
            'price_yearly' => 0,
        ]);
        $paidPlan = $this->createPlan(['slug' => 'paid', 'price_monthly' => 9]);

        $user = User::factory()->create([
            'stripe_id' => 'cus_cancel123',
            'plan_id' => $paidPlan->id,
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

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'plan_id' => $freePlan->id,
        ]);
    }

    public function test_stripe_webhook_with_unknown_customer_does_not_throw(): void
    {
        $listener = new StripeEventListener;

        $listener->handle(new WebhookReceived([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'customer' => 'cus_nonexistent',
                    'metadata' => [
                        'plan_id' => 999,
                        'billing_cycle' => 'lifetime',
                    ],
                ],
            ],
        ]));

        $this->assertTrue(true);
    }
}
