# API Checker Package

API Checker for testing API within a browser.

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/apichecker": "@dev"
```

1.3 To use with Laravel (4.2) add

```php
    '\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider',
```

under 'provider' key in app/config/app.php file.

or (for conditional inclusion) add

```php
if (class_exists('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider')) {
    App::register('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider');
}
```

to app/start/global.php or to app/start/local.php or to another convenient place


## Usage

navigate your browser to /checker url of your domain
