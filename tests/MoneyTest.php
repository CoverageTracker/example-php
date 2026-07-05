<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing\Tests;

use CoverageTracker\Invoicing\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testFromFloatRoundsToCents(): void
    {
        $money = Money::fromFloat(19.999);

        self::assertSame(2000, $money->cents());
    }

    public function testAdd(): void
    {
        $sum = Money::fromFloat(10.50)->add(Money::fromFloat(4.25));

        self::assertSame(14.75, $sum->toFloat());
    }

    public function testSubtractFloorsAtZeroByDefault(): void
    {
        $result = Money::fromFloat(5.00)->subtract(Money::fromFloat(8.00));

        self::assertTrue($result->isZero());
    }

    public function testMultiply(): void
    {
        $result = Money::fromFloat(3.00)->multiply(2.5);

        self::assertSame(7.50, $result->toFloat());
    }

    public function testPercentage(): void
    {
        $result = Money::fromFloat(200.00)->percentage(10.0);

        self::assertSame(20.00, $result->toFloat());
    }

    public function testPercentageRejectsNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::fromFloat(100.00)->percentage(-5.0);
    }

    public function testFormatDefaultsToUsd(): void
    {
        self::assertSame('$19.99', Money::fromFloat(19.99)->format());
    }
}
