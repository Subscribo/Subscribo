# RestProxy Package

Package Subscribo RestProxy is providing proxy functionality to access Subscribo API

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json

(note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key

```json
    "subscribo/RestProxy": "@dev"
```

and update composer

### 1.3 Fore registering RestProxyServiceProvider with Laravel (5.0),

add the following under 'provider' key in config/app.php file:

```php
    '\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider',
```

or add following (for conditional registration) to bootstrap/app.php


```php
    if (class_exists('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider')) {
        $app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');
    }
```

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps might not be necessary.

### 1.4 [Configure](../restclient/README.md) Package Subscribo RestClient:

setup token ring to be used (e.g. by setting SUBSCRIBO_REST_CLIENT_TOKEN_RING=your_token_ring in appropriate .env file)

If you don't have your token ring, contact your Subscribo Administrator.

You might need to setup also other Rest Client settings if you are not using the defaults.

### 1.5 [Setup](../apiclientauth/README.md) Package Subscribo ApiClientAuth:

Set driver configuration to 'remote' in config/auth.php:

```php
    'driver' => 'remote',
```

