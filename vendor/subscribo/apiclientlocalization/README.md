# Api Client Localization Package

Helper package for localization of API Clients, to be used in Frontend Servers

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
    "subscribo/apiclientlocalization": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ApiClientLocalizationServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ApiClientLocalization\\Integration\\Laravel\\ApiClientLocalizationServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ApiClientLocalization\\Integration\\Laravel\\ApiClientLocalizationServiceProvider')) {
        $app->register('\\Subscribo\\ApiClientLocalization\\Integration\\Laravel\\ApiClientLocalizationServiceProvider');
    }
```

### 1.4 To publish default configuration (possibly also config tagged files from other packages) to application run

```sh
    php artisan vendor:publish --tag config
```

or (to force overwrite existing file, and only from this package)

```sh
    php artisan vendor:publish --tag="config" --provider="\\Subscribo\\DevelopmentSeeder\\Integration\\Laravel\\ApiClientLocalizationServiceProvider" --force
```

### 1.5 Publishing Views

for publishing package views your can run

```sh
    php artisan vendor:publish --tag view
```
or (to force overwrite existing files, and only from this package)

```sh
    php artisan vendor:publish --tag="view" --provider="\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientLocalizationServiceProvider" --force
```

### 1.6 Publishing Translations

for publishing package translations / general translations for application your can run

```sh
    php artisan vendor:publish --tag translation
```
or (to force overwrite existing files, and only from this package)

```sh
    php artisan vendor:publish --tag="translation" --provider="\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientLocalizationServiceProvider" --force
```
