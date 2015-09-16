# Billing Package

Umbrella package for billing, charging, invoicing and payment functionality

## 1. Installation

### 1.1 Setup your project's composer.json

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

(Note: you need to have access to this repository as well as to resources it points to)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev"
```

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/billing": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register BillingServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\Billing\\Integration\\Laravel\\BillingServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\Billing\\Integration\\Laravel\\BillingServiceProvider')) {
        $app->register('\\Subscribo\\Billing\\Integration\\Laravel\\BillingServiceProvider');
    }
```

