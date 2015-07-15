# Localization package

Internationalization and localization support

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
    "subscribo/localization": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register LocalizationServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider')) {
        $app->register('\\Subscribo\\Localization\\Integration\\Laravel\\LocalizationServiceProvider');
    }
```

## 2. Usage

### 2.1 Placeholder recommendation:

For replacing values set by administrator in database preferably use braces surrounding {placeholder}.

For replacing user provided values preferably use percent signs surrounding %placeholder%.

(Note: just using percent sign does not automatically sanitize values.)
