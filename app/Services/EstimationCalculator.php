<?php

namespace App\Services;

use App\Models\Block;
use App\Models\Estimation;

class EstimationCalculator
{
    public function calculateTotals(Estimation $estimation): array
    {
        $totals = [
            'setup' => 0,
            'programming' => 0,
            'integration' => 0,
            'field_creation' => 0,
            'content_management' => 0,
            'translation' => 0,
            'addons' => 0,
            'total_time' => 0,
            'total_price' => 0,
        ];

        // 1. Setup
        if ($estimation->setup) {
            $estimation->setup->load('prices');
            if ($estimation->type === 'hour') {
                $totals['setup'] = (float) ($estimation->setup->fixed_hours ?? 0);
            } else {
                $totals['setup'] = $estimation->setup->priceForCurrency($estimation->currency ?? 'EUR');
            }
        }

        // Determine the currency key for block price lookup
        // For fixed estimations with hourly_rate, fall back to HOUR price sets
        $useHourFallback = $estimation->type === 'fixed' && $estimation->hourly_rate > 0;
        $currencyKey = ($estimation->type === 'hour' || $useHourFallback) ? 'HOUR' : ($estimation->currency ?? 'EUR');
        $hourlyRate = (float) ($estimation->hourly_rate ?? 0);

        // 2. Identify unique blocks for Programming + Integration
        $uniqueBlockIds = [];
        $blockInstances = [];

        foreach ($estimation->pages as $page) {
            $pageQty = (int) ($page->quantity ?? 1);
            foreach ($page->blocks as $block) {
                $uniqueBlockIds[] = $block->id;

                $instance = clone $block;
                $instance->page_quantity = $pageQty;
                $blockInstances[] = $instance;
            }
        }

        $uniqueBlockIds = array_unique($uniqueBlockIds);

        // Count Programming + Integration only once per block type
        foreach ($uniqueBlockIds as $blockId) {
            $block = Block::with('priceSets')->find($blockId);
            $priceSet = $block->priceSetFor($currencyKey);

            // Fallback to HOUR price sets if no matching currency price set
            if ($priceSet === null && $estimation->type === 'fixed') {
                $priceSet = $block->priceSetFor('HOUR');
            }

            $progValue = (float) ($priceSet?->price_programming ?? 0);
            $integValue = (float) ($priceSet?->price_integration ?? 0);

            $totals['programming'] += $useHourFallback ? $progValue * $hourlyRate : $progValue;
            $totals['integration'] += $useHourFallback ? $integValue * $hourlyRate : $integValue;
        }

        // 3. Calculate Field Creation + Content Management for each instance
        foreach ($blockInstances as $instance) {
            $qty = (int) $instance->pivot->quantity;
            $pageQty = (int) $instance->page_quantity;

            $priceSet = $instance->priceSetFor($currencyKey);

            // Fallback to HOUR price sets if no matching currency price set
            if ($priceSet === null && $estimation->type === 'fixed') {
                $priceSet = $instance->priceSetFor('HOUR');
            }

            $field_creation = (float) ($priceSet?->price_field_creation ?? 0);
            $content_management = (float) ($priceSet?->price_content_management ?? 0);

            if ($useHourFallback) {
                $field_creation *= $hourlyRate;
                $content_management *= $hourlyRate;
            }

            $totals['field_creation'] += $field_creation * $qty;
            $totals['content_management'] += $content_management * $qty * $pageQty;
        }

        // 4. Translation
        $user = auth()->user();
        $subscription = $user?->activeSubscription;
        $plan = $subscription?->plan;
        $canTranslate = $plan ? $plan->has_translation_module : true;

        if ($estimation->translation_enabled && $canTranslate) {
            $percentage = (float) ($estimation->translation_percentage ?? 0);
            $languages = (int) ($estimation->translation_languages_count ?? 1);

            // Si plus d'une langue, on multiplie la surcharge par 2 comme demandé
            if ($languages > 1) {
                $percentage = $percentage * 2;
            }

            $fixed = 0;
            // Choix du montant fixe selon le type d'estimation
            if ($estimation->type === 'hour') {
                $fixed_hours = (float) ($estimation->translation_fixed_hours ?? 0);
                if ($estimation->hourly_rate) {
                    $fixed = $fixed_hours * (float) $estimation->hourly_rate;
                }
            } else {
                $fixed = (float) ($estimation->translation_fixed_price ?? 0);
            }

            $content_base = $totals['field_creation'] + $totals['content_management'];
            $totals['translation'] = $fixed + ($content_base * ($percentage / 100));
        }

        // 5. Add-ons
        $addonDetails = [];
        foreach ($estimation->addons as $addon) {
            $value = 0;
            if ($addon->type === 'fixed_price') {
                $value = (float) $addon->value;
            } elseif ($addon->type === 'fixed_hours') {
                if ($estimation->type === 'hour' && $estimation->hourly_rate) {
                    $value = (float) $addon->value * (float) $estimation->hourly_rate;
                } else {
                    $value = (float) $addon->value;
                }
            } else {
                $base_value = $this->getAddonBaseValue($addon->calculation_base, $totals);
                $value = $base_value * ((float) $addon->value / 100);
            }
            $totals['addons'] += $value;
            $addonDetails[] = [
                'name' => $addon->name,
                'type' => $addon->type,
                'value' => $addon->value,
                'calculated_value' => $value,
                'calculation_base' => $addon->calculation_base,
            ];
        }
        $totals['addon_details'] = $addonDetails;

        // 6. Final Totals
        $grand_total = $totals['setup'] +
                      $totals['programming'] +
                      $totals['integration'] +
                      $totals['field_creation'] +
                      $totals['content_management'] +
                      $totals['translation'] +
                      $totals['addons'];

        if ($estimation->type === 'hour') {
            $totals['total_time'] = $grand_total;

            // On calcule le prix
            if ($estimation->hourly_rate) {
                // Le grand_total contient déjà les addons convertis en prix si fixed_hours
                // Attention: si fixed_hours a été ajouté au grand_total en tant qu'heures, c'est bon.
                // Si c'était déjà en prix, ça fausse le total_time.

                // Reprenons proprement pour total_time
                $total_hours = $totals['setup'] +
                              $totals['programming'] +
                              $totals['integration'] +
                              $totals['field_creation'] +
                              $totals['content_management'];

                // On ajoute les addons en heures
                foreach ($estimation->addons as $addon) {
                    if ($addon->type === 'fixed_hours') {
                        $total_hours += (float) $addon->value;
                    }
                }

                // On ajoute la traduction si elle est en heures (si type d'estimation est heure)
                if ($estimation->translation_enabled && $estimation->type === 'hour') {
                    $total_hours += (float) ($estimation->translation_fixed_hours ?? 0);
                }

                $totals['total_time'] = $total_hours;
                $totals['total_price'] = ($total_hours * (float) $estimation->hourly_rate) + $totals['translation'];

                // Ajouter les addons qui sont déjà en prix (fixed_price ou percentage)
                foreach ($estimation->addons as $addon) {
                    if ($addon->type === 'fixed_price') {
                        $totals['total_price'] += (float) $addon->value;
                    } elseif ($addon->type === 'percentage') {
                        $base_value = $this->getAddonBaseValue($addon->calculation_base, $totals);
                        $totals['total_price'] += $base_value * ((float) $addon->value / 100);
                    }
                }
            } else {
                $totals['total_price'] = 0;
            }
        } else {
            $totals['total_time'] = 0; // Or calculate if needed
            $totals['total_price'] = $grand_total;
        }

        return $totals;
    }

    private function getAddonBaseValue(string $base, array $totals): float
    {
        return match ($base) {
            'global' => $totals['setup'] + $totals['programming'] + $totals['integration'] + $totals['field_creation'] + $totals['content_management'],
            'blocks' => $totals['programming'] + $totals['integration'],
            'pages' => $totals['field_creation'] + $totals['content_management'],
            'content' => $totals['content_management'],
            'content_fields' => $totals['field_creation'] + $totals['content_management'],
            default => 0,
        };
    }
}
