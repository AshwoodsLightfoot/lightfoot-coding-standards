# Lightfoot Coding Standard

Custom PHP CodeSniffer (PHPCS) standard based on PSR-12.

## Installation

```bash
composer require ashwoods-lightfoot/lightfoot-coding-standard --dev
```

## Usage

### Command Line

```bash
vendor/bin/phpcs --standard=LightfootCodingStandard /path/to/your/code
```

### PhpStorm Integration

1. Go to Settings > Editor > Inspections
2. Find "PHP_CodeSniffer validation" under "PHP"
3. Check the box to enable it
4. Click on the "..." button next to "Coding standard"
5. Select "Custom" and provide the path to the ruleset.xml file in this package

## Included Sniffs

### ControlStructures

#### LogicalOperatorLinePositionSniff

Enforces logical operators (&&, ||) to appear at the start of a new line in multi-line conditions.

**Valid:**
```php
if ($condition1
    && $condition2) {
    // Code
}
```

**Invalid:**
```php
if ($condition1 &&
    $condition2) {
    // Code
}
```

## Running Tests

To run the tests for this coding standard:

```bash
composer install
composer test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add or modify sniffs
4. Add or update tests
5. Submit a pull request
