# Subscribo ClientCheckoutCommon Package

Common files for Checkout functionality for Subscribo API client

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

### 1.2 Add dependency to this package under "require" key of your project's or package's composer.json

```json
    "subscribo/client-checkout-common": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 Register ClientCheckoutCommonServiceProvider in your project or package

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ClientCheckoutCommon\\Integration\\Laravel\\ClientCheckoutCommonServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ClientCheckoutCommon\\Integration\\Laravel\\ClientCheckoutCommonServiceProvider')) {
        $app->register('\\Subscribo\\ClientCheckoutCommon\\Integration\\Laravel\\ClientCheckoutCommonServiceProvider');
    }
```

or (for package) in `YourPackageServiceProvider::register()`:

```php
    $this->app->register('\\Subscribo\\ClientCheckoutCommon\\Integration\\Laravel\\ClientCheckoutCommonServiceProvider');
```

