<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing;

use InvalidArgumentException;

/**
 * An immutable monetary amount, stored as integer cents to avoid float
 * rounding errors.
 */
final class Money
{
    private const SYMBOLS = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
    ];

    private function __construct(private readonly int $cents)
    {
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public static function fromFloat(float $amount): self
    {
        return new self((int) round($amount * 100));
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    public function add(Money $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(Money $other, bool $allowNegative = false): self
    {
        $result = $this->cents - $other->cents;

        if ($result < 0 && !$allowNegative) {
            return new self(0);
        }

        return new self($result);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->cents * $factor));
    }

    public function percentage(float $percent): self
    {
        if ($percent < 0) {
            throw new InvalidArgumentException("Percentage cannot be negative: {$percent}");
        }

        return $this->multiply($percent / 100);
    }

    public function isZero(): bool
    {
        return $this->cents === 0;
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function greaterThan(Money $other): bool
    {
        return $this->cents > $other->cents;
    }

    public function format(string $currency = 'USD'): string
    {
        $amount = number_format($this->cents / 100, 2);

        if (isset(self::SYMBOLS[$currency])) {
            return self::SYMBOLS[$currency] . $amount;
        }

        return strtoupper($currency) . ' ' . $amount;
    }
}
