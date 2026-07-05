<?php

declare(strict_types=1);

namespace CoverageTracker\Invoicing;

use RuntimeException;

/**
 * An invoice: a set of line items, an optional discount, and a tax
 * jurisdiction. Computes subtotal, discount, tax, and grand total.
 */
final class Invoice
{
    /** @var LineItem[] */
    private array $items = [];

    private ?Discount $discount = null;

    public function __construct(private readonly string $taxJurisdiction = 'US-OR')
    {
    }

    public function addItem(LineItem $item): void
    {
        $this->items[] = $item;
    }

    public function applyDiscount(Discount $discount): void
    {
        $this->discount = $discount;
    }

    public function subtotal(): Money
    {
        if ($this->items === []) {
            throw new RuntimeException('Cannot total an invoice with no line items');
        }

        return array_reduce(
            $this->items,
            fn (Money $carry, LineItem $item) => $carry->add($item->subtotal()),
            Money::fromCents(0),
        );
    }

    public function discountAmount(): Money
    {
        $subtotal = $this->subtotal();

        if ($this->discount === null) {
            return Money::fromCents(0);
        }

        return $subtotal->subtract($this->discount->apply($subtotal), true);
    }

    public function taxAmount(TaxCalculator $calculator): Money
    {
        $taxable = $this->subtotal()->subtract($this->discountAmount(), true);

        return $calculator->calculate($taxable, $this->taxJurisdiction);
    }

    public function total(TaxCalculator $calculator): Money
    {
        $subtotal = $this->subtotal();
        $afterDiscount = $subtotal->subtract($this->discountAmount(), true);

        return $afterDiscount->add($this->taxAmount($calculator));
    }

    public function itemCount(): int
    {
        return count($this->items);
    }

    /**
     * @return array{subtotal: string, discount: string, tax: string, total: string}
     */
    public function summary(TaxCalculator $calculator, bool $verbose = false): array
    {
        $summary = [
            'subtotal' => $this->subtotal()->format(),
            'discount' => $this->discountAmount()->format(),
            'tax' => $this->taxAmount($calculator)->format(),
            'total' => $this->total($calculator)->format(),
        ];

        if ($verbose) {
            $summary['jurisdiction'] = $this->taxJurisdiction;
            $summary['item_count'] = (string) $this->itemCount();
        }

        return $summary;
    }
}
