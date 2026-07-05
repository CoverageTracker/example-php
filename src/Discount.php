<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing;

use InvalidArgumentException;

/**
 * A discount applied to an invoice subtotal — either a percentage off, or a
 * fixed amount off (floored at zero, never pushing a subtotal negative).
 */
final class Discount
{
    public const TYPE_PERCENTAGE = 'percentage';
    public const TYPE_FIXED = 'fixed';

    /** Known promotional codes mapped to their discount definition. */
    private const CODES = [
        'SAVE10' => [self::TYPE_PERCENTAGE, 10.0],
        'SAVE25' => [self::TYPE_PERCENTAGE, 25.0],
        'FLAT5' => [self::TYPE_FIXED, 5.0],
    ];

    public function __construct(
        private readonly string $type,
        private readonly float $value,
    ) {
        if (!in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_FIXED], true)) {
            throw new InvalidArgumentException("Unknown discount type: {$type}");
        }

        if ($value < 0) {
            throw new InvalidArgumentException('Discount value cannot be negative');
        }
    }

    public static function fromCode(string $code): self
    {
        $definition = self::CODES[strtoupper($code)] ?? null;

        if ($definition === null) {
            throw new InvalidArgumentException("Unknown promo code: {$code}");
        }

        [$type, $value] = $definition;

        return new self($type, $value);
    }

    public function apply(Money $subtotal): Money
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            $percent = min($this->value, 100.0);

            return $subtotal->subtract($subtotal->percentage($percent));
        }

        return $subtotal->subtract(Money::fromFloat($this->value));
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): float
    {
        return $this->value;
    }
}
