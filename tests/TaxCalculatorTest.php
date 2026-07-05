<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing\Tests;

use CoverageTracker\Invoicing\Money;
use CoverageTracker\Invoicing\TaxCalculator;
use PHPUnit\Framework\TestCase;

final class TaxCalculatorTest extends TestCase
{
    public function testCalculatesTaxForKnownJurisdiction(): void
    {
        $calculator = new TaxCalculator();
        $tax = $calculator->calculate(Money::fromFloat(100.00), 'US-CA');

        self::assertSame(8.50, $tax->toFloat());
    }

    public function testExemptsAmountsBelowDeMinimisThreshold(): void
    {
        $calculator = new TaxCalculator();
        $tax = $calculator->calculate(Money::fromFloat(0.50), 'US-CA');

        self::assertTrue($tax->isZero());
    }
}
