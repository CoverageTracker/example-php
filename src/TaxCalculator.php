<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing;

/**
 * Tiered sales tax lookup by jurisdiction code. Amounts below the
 * de-minimis threshold are exempt in every jurisdiction.
 */
final class TaxCalculator
{
    private const DE_MINIMIS_CENTS = 100;

    /** Jurisdiction code => tax rate (fraction, e.g. 0.085 for 8.5%). */
    private const RATES = [
        'US-CA' => 0.085,
        'US-NY' => 0.080,
        'US-OR' => 0.0,
        'EU-DE' => 0.19,
        'EU-FR' => 0.20,
    ];

    public function calculate(Money $amount, string $jurisdiction): Money
    {
        if ($amount->cents() < self::DE_MINIMIS_CENTS) {
            return Money::fromCents(0);
        }

        $rate = self::RATES[$jurisdiction] ?? null;

        if ($rate === null) {
            return Money::fromCents(0);
        }

        return $amount->multiply($rate);
    }

    public function rateFor(string $jurisdiction): float
    {
        return self::RATES[$jurisdiction] ?? 0.0;
    }

    public function isKnownJurisdiction(string $jurisdiction): bool
    {
        return array_key_exists($jurisdiction, self::RATES);
    }
}
