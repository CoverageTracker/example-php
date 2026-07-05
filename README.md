# example-php

[![coverage badge](https://img.shields.io/endpoint?url=https%3A%2F%2Fdemo.coveragetracker.dev%2Fapi%2Fbadge%2FCoverageTracker%2Fexample-php%2Fcoverage.json)](https://demo.coveragetracker.dev/CoverageTracker/example-php?metric=coverage)
[![complexity badge](https://img.shields.io/endpoint?url=https%3A%2F%2Fdemo.coveragetracker.dev%2Fapi%2Fbadge%2FCoverageTracker%2Fexample-php%2Fcomplexity.json)](https://demo.coveragetracker.dev/CoverageTracker/example-php?metric=complexity)

A small, idiomatic PHP invoicing library used as the PHP reference example
for [Coverage Tracker](https://coveragetracker.dev). It exists to give the
PHP row in the
[coverage report generation guide](https://coveragetracker.dev/docs/generating-coverage-reports)
a live, working reference, and to populate the
[demo dashboard](https://demo.coveragetracker.dev) with real trend data.

**This is a demo/marketing repo, not a test suite for Coverage Tracker
itself.**

## What's here

- `src/` — `Money` (integer-cents value object), `Discount` (percentage/fixed,
  promo-code lookup), `TaxCalculator` (tiered jurisdiction rates with a
  de-minimis exemption), `LineItem`, and `Invoice`, each with real branching
  logic.
- `tests/` — a [PHPUnit](https://phpunit.de) suite with several deliberately
  uncovered branches (unknown promo codes, unknown tax jurisdictions,
  guard-clause validation), so `branch_coverage < line_coverage` shows up on
  the dashboard.
- `.github/workflows/coverage.yml` — runs the suite under PHPUnit with
  Xdebug's path-coverage mode, generates a
  [Lizard](https://github.com/terryyin/lizard) complexity report, then
  reports both to the demo instance via the `coverage-tracker` reporting
  Action with `coverage-tool: phpunit` (Cobertura's XML shape doesn't
  self-identify its generator, so the reporter needs to be told to trust
  `branch-rate`).

## Running locally

```sh
composer install
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-cobertura=coverage.xml   # writes coverage.xml
python -m lizard src --xml > lizard-report.xml
```

`phpunit.xml` sets `<coverage pathCoverage="true"/>` — Xdebug only emits
branch data when path coverage is requested; without it PHPUnit's Cobertura
report always writes `branch-rate="0"`.
