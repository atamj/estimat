<?php

namespace App\Enums;

enum Currency: string
{
    case Eur = 'EUR';
    case Usd = 'USD';
    case Cad = 'CAD';
    case Gbp = 'GBP';
    case Chf = 'CHF';

    public function symbol(): string
    {
        return match ($this) {
            self::Eur => '€',
            self::Usd => '$',
            self::Cad => 'CA$',
            self::Gbp => '£',
            self::Chf => 'CHF',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Eur => 'Euro (€)',
            self::Usd => 'Dollar américain ($)',
            self::Cad => 'Dollar canadien (CA$)',
            self::Gbp => 'Livre sterling (£)',
            self::Chf => 'Franc suisse (CHF)',
        };
    }
}
