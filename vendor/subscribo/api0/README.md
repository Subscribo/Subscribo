# API Version 0 Package

API Version 0.

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/api0": "@dev"
```

1.3 To use with Laravel (4.2) add

```php
    '\\Subscribo\\Api0\\Api0ServiceProvider',
```

under 'provider' key in app/config/app.php file.

or (for conditional inclusion) add

```php
if (class_exists('\\Subscribo\\Api0\\Api0ServiceProvider')) {
    App::register('\\Subscribo\\Api0\\Api0ServiceProvider');
}
```

to app/start/global.php or to app/start/local.php or to another convenient place


## Usage

available under url /api/v0/model/{model-name-pluralized} of your domain
