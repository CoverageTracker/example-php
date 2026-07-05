<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing;

use InvalidArgumentException;

/**
 * A single invoice line: a product/service at a unit price and quantity.
 */
final class LineItem
{
    public function __construct(
        private readonly string $description,
        private readonly Money $unitPrice,
        private readonly int $quantity = 1,
    ) {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1');
        }

        if (trim($description) === '') {
            throw new InvalidArgumentException('Description cannot be empty');
        }
    }

    public function description(): string
    {
        return $this->description;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function subtotal(): Money
    {
        return $this->unitPrice->multiply((float) $this->quantity);
    }
}
