# Validation Localization package

Provides Localization of Laravel Validator using Subscribo Localization Localizer

## Important note 1:

If you are registering (directly or indirectly) ValidationLocalizationServiceProvider,
this service provider registers Illuminate\Validation\ValidationServiceProvider for you and overwrite some of its bindings.

## Important note 2:

If using this plugin, custom localized validation strings from resources/lang/en/validation.php might get overriden
 by strings from package or from subscribo/resources/lang/validationlocalization/en/validation.php  (or file with other supported extension, such as .yml)

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
    "subscribo/validationlocalization": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ValidationLocalizationServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ValidationLocalization\\Integration\\Laravel\\ValidationLocalizationServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ValidationLocalization\\Integration\\Laravel\\ValidationLocalizationServiceProvider')) {
        $app->register('\\Subscribo\\ValidationLocalization\\Integration\\Laravel\\ValidationLocalizationServiceProvider');
    }
```

