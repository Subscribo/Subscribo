# Umbrella package for adding Subscribo Thick client functionality to Laravel Project

### Installation:

#### Require package in you project's `composer.json`, lower minimum stability of your project to `dev`
    (but you may want to `prefer-stable`):

```json
    {
        "require": {
            "subscribo/thick-client": "@dev"
        },
        "minimum-stability": "dev",
        "prefer-stable": true
    }
```

### Register `ThickClientServiceProvider` and `ThickClientIntegrationServiceProvider`

e.g. by adding into your `bootstrap/app.php`:

```php
    if (class_exists('\\Subscribo\\ThickClient\\Integration\\Laravel\\ThickClientServiceProvider')) {
        $app->register('\\Subscribo\\ThickClient\\Integration\\Laravel\\ThickClientServiceProvider');
        $app->register('\\Subscribo\\ThickClientIntegration\\Integration\\Laravel\\ThickClientIntegrationServiceProvider');
    }
```

### Add and run migrations

```sh
    php artisan vendor:publish --tag migrations
    php artisan migrate
```
