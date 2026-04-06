<?php

namespace App\Services;

use App\Models\Plan;
use Laravel\Cashier\Cashier;

class StripePlanSyncService
{
    /**
     * Synchronise un plan avec Stripe : crée ou met à jour le Product Stripe,
     * puis crée les Price Stripe manquants ou archivés les anciens si le montant a changé.
     */
    public function sync(Plan $plan, array $oldPrices = []): void
    {
        if (! config('cashier.secret')) {
            return;
        }

        $stripe = Cashier::stripe();

        $productId = $this->syncProduct($stripe, $plan);

        $plan->stripe_product_id = $productId;
        $plan->stripe_monthly_price_id = $this->syncPrice(
            $stripe,
            $productId,
            $plan->stripe_monthly_price_id,
            (float) $plan->price_monthly,
            (float) ($oldPrices['price_monthly'] ?? $plan->price_monthly),
            'month',
            $plan->name.' — Mensuel'
        );

        $plan->stripe_yearly_price_id = $this->syncPrice(
            $stripe,
            $productId,
            $plan->stripe_yearly_price_id,
            (float) $plan->price_yearly,
            (float) ($oldPrices['price_yearly'] ?? $plan->price_yearly),
            'year',
            $plan->name.' — Annuel'
        );

        if ($plan->price_lifetime > 0) {
            $plan->stripe_lifetime_price_id = $this->syncOneTimePrice(
                $stripe,
                $productId,
                $plan->stripe_lifetime_price_id,
                (float) $plan->price_lifetime,
                (float) ($oldPrices['price_lifetime'] ?? $plan->price_lifetime),
                $plan->name.' — À vie'
            );
        } else {
            $plan->stripe_lifetime_price_id = null;
        }

        $plan->saveQuietly();
    }

    /**
     * Crée ou met à jour le Product Stripe associé au plan.
     */
    private function syncProduct(\Stripe\StripeClient $stripe, Plan $plan): string
    {
        if ($plan->stripe_product_id) {
            $stripe->products->update($plan->stripe_product_id, [
                'name' => $plan->name,
                'description' => $plan->description ?: null,
                'active' => $plan->is_active,
            ]);

            return $plan->stripe_product_id;
        }

        $product = $stripe->products->create([
            'name' => $plan->name,
            'description' => $plan->description ?: null,
            'metadata' => [
                'app' => config('app.name'),
                'app_url' => config('app.url'),
                'plan_slug' => $plan->slug,
            ],
        ]);

        return $product->id;
    }

    /**
     * Crée un Price récurrent (monthly/yearly) ou réutilise l'existant.
     * Si le montant a changé, archive l'ancien et crée un nouveau.
     * Les prix à 0€ sont autorisés (plan gratuit).
     */
    private function syncPrice(
        \Stripe\StripeClient $stripe,
        string $productId,
        ?string $existingPriceId,
        float $newAmount,
        float $oldAmount,
        string $interval,
        string $nickname
    ): ?string {
        if ($newAmount < 0) {
            return null;
        }

        $amountInCents = (int) round($newAmount * 100);

        if ($existingPriceId && abs($newAmount - $oldAmount) < 0.001) {
            return $existingPriceId;
        }

        if ($existingPriceId) {
            $stripe->prices->update($existingPriceId, ['active' => false]);
        }

        $price = $stripe->prices->create([
            'product' => $productId,
            'unit_amount' => $amountInCents,
            'currency' => 'eur',
            'recurring' => ['interval' => $interval],
            'nickname' => $nickname,
        ]);

        return $price->id;
    }

    /**
     * Crée un Price one-time (lifetime) ou réutilise l'existant.
     */
    private function syncOneTimePrice(
        \Stripe\StripeClient $stripe,
        string $productId,
        ?string $existingPriceId,
        float $newAmount,
        float $oldAmount,
        string $nickname
    ): ?string {
        if ($newAmount <= 0) {
            return null;
        }

        $amountInCents = (int) round($newAmount * 100);

        if ($existingPriceId && abs($newAmount - $oldAmount) < 0.001) {
            return $existingPriceId;
        }

        if ($existingPriceId) {
            $stripe->prices->update($existingPriceId, ['active' => false]);
        }

        $price = $stripe->prices->create([
            'product' => $productId,
            'unit_amount' => $amountInCents,
            'currency' => 'eur',
            'nickname' => $nickname,
        ]);

        return $price->id;
    }
}
