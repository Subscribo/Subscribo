# API Checker Package

API Checker for testing API within a browser.

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json.

(note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add dependency on this package to your project's composer.json under "require" or "require-dev" keys

```json
    "subscribo/apichecker": "@dev"
```

#### 1.3.1 To use with Laravel 4.2

add

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

#### 1.3.2 To use with Laravel 5.0:

add

```php
    '\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider')) {
        $app->register('\\Subscribo\\ApiChecker\\ApiCheckerServiceProvider');
    }
```

in bootstrap/app.php for conditional registration


Note: If used with package adding this dependency and/or registering this service provider for you, respective steps are not necessary.

## 2. Usage

Navigate your browser to /checker url of your domain (or to uri defined in your config files).

You might need to provide correct Access Token in the form in order to get API response.
