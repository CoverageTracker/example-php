<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing\Tests;

use CoverageTracker\Invoicing\Discount;
use CoverageTracker\Invoicing\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DiscountTest extends TestCase
{
    public function testPercentageDiscount(): void
    {
        $discount = new Discount(Discount::TYPE_PERCENTAGE, 10.0);
        $result = $discount->apply(Money::fromFloat(100.00));

        self::assertSame(90.00, $result->toFloat());
    }

    public function testPercentageDiscountClampsAt100(): void
    {
        $discount = new Discount(Discount::TYPE_PERCENTAGE, 150.0);
        $result = $discount->apply(Money::fromFloat(100.00));

        self::assertTrue($result->isZero());
    }

    public function testConstructorRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Discount('buy-one-get-one', 10.0);
    }

    public function testConstructorRejectsNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Discount(Discount::TYPE_FIXED, -5.0);
    }
}
