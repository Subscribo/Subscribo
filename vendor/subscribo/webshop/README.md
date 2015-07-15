# Subscribo Webshop Package

A simple Webshop using Subscribo system

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
    "subscribo/webshop": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register WebshopServiceProvider with Laravel (5.1)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider')) {
        $app->register('\\Subscribo\\Webshop\\Integration\\Laravel\\WebshopServiceProvider');
    }
```

