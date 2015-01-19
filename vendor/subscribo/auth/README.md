# Subscribo Authentication Package

contain functionality connected to user authentication

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key

```json
    "subscribo/auth": "@dev",
```

(Note: do not add trailing comma if it is the last item listed)

### 1.3 If using Auth Handler functionality, you might need to add (to the same place) also packages suggested in composer.json

(with "@dev" version specification), especially:

```json
    "subscribo/environment": "@dev",
    "subscribo/serviceprovider": "@dev",
```

### 1.4 If you are using Laravel (5.0), you might want to register AuthServiceProvider:

To do so, add

```php
    '\\Subscribo\\Auth\\Integration\\Laravel\\AuthServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\Auth\\Integration\\Laravel\\AuthServiceProvider')) {
        $app->register('\\Subscribo\\Auth\\Integration\\Laravel\\AuthServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps might not be necessary.

## 2. Usage

