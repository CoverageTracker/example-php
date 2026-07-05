<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing\Tests;

use CoverageTracker\Invoicing\Discount;
use CoverageTracker\Invoicing\Invoice;
use CoverageTracker\Invoicing\LineItem;
use CoverageTracker\Invoicing\Money;
use CoverageTracker\Invoicing\TaxCalculator;
use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    public function testSubtotalSumsLineItems(): void
    {
        $invoice = new Invoice();
        $invoice->addItem(new LineItem('Widget', Money::fromFloat(10.00), 2));
        $invoice->addItem(new LineItem('Gadget', Money::fromFloat(5.00)));

        self::assertSame(25.00, $invoice->subtotal()->toFloat());
        self::assertSame(2, $invoice->itemCount());
    }

    public function testTotalAppliesDiscountAndTax(): void
    {
        $invoice = new Invoice('US-CA');
        $invoice->addItem(new LineItem('Widget', Money::fromFloat(100.00)));
        $invoice->applyDiscount(new Discount(Discount::TYPE_PERCENTAGE, 10.0));

        $calculator = new TaxCalculator();

        // subtotal 100 -> discount 10 -> taxable 90 -> tax 8.5% = 7.65 -> total 97.65
        self::assertSame(10.00, $invoice->discountAmount()->toFloat());
        self::assertSame(7.65, $invoice->taxAmount($calculator)->toFloat());
        self::assertSame(97.65, $invoice->total($calculator)->toFloat());
    }

    public function testTotalWithNoDiscount(): void
    {
        $invoice = new Invoice('US-OR');
        $invoice->addItem(new LineItem('Widget', Money::fromFloat(50.00)));

        $calculator = new TaxCalculator();

        self::assertTrue($invoice->discountAmount()->isZero());
        self::assertSame(50.00, $invoice->total($calculator)->toFloat());
    }

    public function testSummaryReturnsFormattedFigures(): void
    {
        $invoice = new Invoice('US-OR');
        $invoice->addItem(new LineItem('Widget', Money::fromFloat(20.00)));

        $summary = $invoice->summary(new TaxCalculator());

        self::assertSame('$20.00', $summary['subtotal']);
        self::assertSame('$20.00', $summary['total']);
        self::assertArrayNotHasKey('jurisdiction', $summary);
    }
}
