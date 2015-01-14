# Client Checker Package

Client Checker for testing communicating with API from a browser.

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json.

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add dependency on this package to your project's composer.json under "require" or "require-dev" keys

```json
    "subscribo/clientchecker": "@dev"
```

### 1.3 To use with Laravel 5.0

add

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


## 2. Usage

Navigate your browser to /client url of your domain (or whatever url is defined in your config files)
