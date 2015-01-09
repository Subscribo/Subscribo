# Client Checker Package

Client Checker for testing communicating with API from a browser.

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" or "require-dev" keys

```json
    "subscribo/clientchecker": "@dev"
```

1.3 To use with Laravel (5.0) add

```php
    '\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider',
```

under 'provider' key in config/app.php file.

or (for conditional inclusion) add

```php
if (class_exists('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider')) {
    $app->register('\\Subscribo\\ClientChecker\\ClientCheckerServiceProvider');
}
```

to bootstrap/app.php or to another convenient place


## Usage

navigate your browser to /client url of your domain (or whatever url is defined in your config files)
