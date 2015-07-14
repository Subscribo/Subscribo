# Api Client Common Package

Common classes for API Client packages, to be used in Frontend Servers

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
    "subscribo/apiclientcommon": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ApiClientCommonServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider')) {
        $app->register('\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider');
    }
```

### 1.4 Registering Routes

If you have registered ApiClientCommonServiceProvider within other service provider register() method, you might want to save its instance (e.g. into a private attribute)
and in the boot() method of that service provide register /question routes using registerRoutes() method.
For implementation example you can see (ApiClientAuthServiceProvider)[../apiclientauth/src/Subscribo/ApiClientAuth/Integration/Laravel/ApiClientAuthServiceProvider.php]

### 1.5 Publishing Views

for publishing package views your can run

```sh
    php artisan vendor:publish --tag view
```
or (to force overwrite existing files, and only from this package)

```sh
    php artisan vendor:publish --tag="view" --provider="\\Subscribo\\ApiClientCommon\\Integration\\Laravel\\ApiClientCommonServiceProvider" --force
```
