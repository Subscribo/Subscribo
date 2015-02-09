# Api Client Package

Umbrella package for API Client functionality, to be used in Frontend Servers

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/apiclient": "@dev"
```

### 1.3 If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider')) {
        $app->register('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, the respective steps are not necessary.

