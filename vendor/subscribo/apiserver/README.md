# Api Server Package

Umbrella package for API Server functionality, to be used in Backend Servers

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/apiserver": "@dev"
```

### 1.3 If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\ApiServer\\Integration\\Laravel\\ApiServerServiceProvider',
```

under 'providers' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\ApiServer\\Integration\\Laravel\\ApiServerServiceProvider')) {
        $app->register('\\Subscribo\\ApiServer\\Integration\\Laravel\\ApiServerServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, the respective steps are not necessary.

