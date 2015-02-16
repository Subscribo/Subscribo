# Subscribo Authentication Package for API Client

contain functionality connected to customer authentication

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key

```json
    "subscribo/apiclientauth": "@dev",
```

(Note: do not add trailing comma if it is the last item listed)

### 1.3 To use with Laravel (5.0), register ApiClientAuthServiceProvider:

To do so, add

```php
    '\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider')) {
        $app->register('\\Subscribo\\ApiClientAuth\\Integration\\Laravel\\ApiClientAuthServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps might not be necessary.

### 1.4 Set driver configuration to 'remote' in config/auth.php:

```php
    'driver' => 'remote',
```


### 1.5 Important: You need to properly [configure](../restclient/README.md) Subscribo RestClient package

## 2. Usage

### 2.1 Traits

You may want to use trait \Subscribo\ApiClientAuth\Traits\AuthenticatesAndRegistersUsersTrait
in app/Http/Controllers/Auth/AuthController.php
(instead of original trait AuthenticatesAndRegistersUsers) in order to handle possible exceptions / errors which might happen during servers communicating during login
