<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing\Tests;

use CoverageTracker\Invoicing\LineItem;
use CoverageTracker\Invoicing\Money;
use PHPUnit\Framework\TestCase;

final class LineItemTest extends TestCase
{
    public function testSubtotalMultipliesUnitPriceByQuantity(): void
    {
        $item = new LineItem('Widget', Money::fromFloat(5.00), 3);

        self::assertSame(15.00, $item->subtotal()->toFloat());
    }
}
