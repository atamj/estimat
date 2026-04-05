<?php

namespace Tests\Feature\Auth;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_shows_hidden_plan_fields_from_query_string(): void
    {
        $response = $this->get('/register?plan=pro&billing_cycle=yearly');

        $response->assertOk();
        $response->assertSee('name="plan"', false);
        $response->assertSee('value="pro"', false);
        $response->assertSee('name="billing_cycle"', false);
        $response->assertSee('value="yearly"', false);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('estimations.index', absolute: false));
    }

    public function test_registration_with_paid_plan_and_stripe_price_redirects_to_subscription_with_pending_checkout(): void
    {
        Plan::create([
            'name' => 'Gratuit',
            'slug' => 'free',
            'description' => 'Free',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'price_lifetime' => null,
            'stripe_product_id' => null,
            'stripe_monthly_price_id' => null,
            'stripe_yearly_price_id' => null,
            'stripe_lifetime_price_id' => null,
            'max_estimations' => 5,
            'max_blocks' => 10,
            'has_white_label_pdf' => false,
            'has_translation_module' => false,
            'is_active' => true,
        ]);

        $proPlan = Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Pro',
            'price_monthly' => 19,
            'price_yearly' => 149,
            'price_lifetime' => null,
            'stripe_product_id' => 'prod_test',
            'stripe_monthly_price_id' => 'price_monthly_test',
            'stripe_yearly_price_id' => 'price_yearly_test',
            'stripe_lifetime_price_id' => null,
            'max_estimations' => -1,
            'max_blocks' => -1,
            'has_white_label_pdf' => true,
            'has_translation_module' => true,
            'is_active' => true,
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plan' => 'pro',
            'billing_cycle' => 'monthly',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('subscription', absolute: false));
        $response->assertSessionHas('pending_checkout', [
            'plan_id' => $proPlan->id,
            'billing_cycle' => 'monthly',
        ]);
    }

    public function test_registration_with_paid_plan_without_stripe_price_goes_to_dashboard(): void
    {
        Plan::create([
            'name' => 'Gratuit',
            'slug' => 'free',
            'description' => 'Free',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'price_lifetime' => null,
            'stripe_product_id' => null,
            'stripe_monthly_price_id' => null,
            'stripe_yearly_price_id' => null,
            'stripe_lifetime_price_id' => null,
            'max_estimations' => 5,
            'max_blocks' => 10,
            'has_white_label_pdf' => false,
            'has_translation_module' => false,
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'description' => 'Pro',
            'price_monthly' => 19,
            'price_yearly' => 149,
            'price_lifetime' => null,
            'stripe_product_id' => null,
            'stripe_monthly_price_id' => null,
            'stripe_yearly_price_id' => null,
            'stripe_lifetime_price_id' => null,
            'max_estimations' => -1,
            'max_blocks' => -1,
            'has_white_label_pdf' => true,
            'has_translation_module' => true,
            'is_active' => true,
        ]);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plan' => 'pro',
            'billing_cycle' => 'monthly',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('estimations.index', absolute: false));
        $response->assertSessionMissing('pending_checkout');
    }
}
