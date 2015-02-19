# Development Seeder Package

Contain Seeders for development environment

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
    "subscribo/developmentseeder": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register DevelopmentSeederServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider')) {
        $app->register('\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider');
    }
```

### 1.4 You also need to properly configure SUBSCRIBO_COMMON_SECRET environment variable, for more details you can see (Subscribo RestCommon Install docs, point 1.4)[../restcommon/README.md#14-subscribo-common-secret]

### 1.5 To publish seeders (possibly also seeders tagged files from other packages) to application run

```sh
    php artisan vendor:publish --tag seeds
```

or (to force overwrite existing file, and only from this package)

```sh
    php artisan vendor:publish --tag="seeds" --provider="\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\DevelopmentSeederServiceProvider" --force
```


### 1.6 To generate seeds run

```sh
    php artisan db:seeds
```

Note: you might need to run or refresh migrations before, you can see [Laravel documentation](http://laravel.com/docs/5.0/migrations) for details.
