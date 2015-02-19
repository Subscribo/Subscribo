# Api Client Package

Umbrella package for API Client functionality, to be used in Frontend Servers

## 1. Installation

### 1.1 Setup your project's composer.json

Add repository containing this package

```json
    "repositories": [{"type": "composer", "url": "http://your.resource.url"}],
```

(Note: you need to have access to this repository as well as to resources it points to)

Set minimum stability to 'dev':

```json
    "minimum-stability": "dev"
```

### 1.2 Add dependency to this package under "require" key of your project's composer.json

```json
    "subscribo/apiclient": "@dev"
```

and update composer

```sh
    composer update
```

### 1.3 To register ApiClientServiceProvider with Laravel (5.0)

add following under 'providers' key in config/app.php file:

```php
    '\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider',
```

or (for conditional registration) you can add following to bootstrap/app.php:

```php
    if (class_exists('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider')) {
        $app->register('\\Subscribo\\ApiClient\\Integration\\Laravel\\ApiClientServiceProvider');
    }
```

### 1.4 [Configure](../restclient/README.md) Package Subscribo RestClient:

setup token ring to be used (e.g. by setting SUBSCRIBO_REST_CLIENT_TOKEN_RING=your_token_ring in appropriate .env file)

If you don't have your token ring, contact your Subscribo Administrator.

You might need to setup also other Rest Client settings if you are not using the defaults.

### 1.5 [Setup](../apiclientauth/README.md) Package Subscribo ApiClientAuth:

Set driver configuration to 'remote' in config/auth.php:

```php
    'driver' => 'remote',
```

### 1.6 To use default Laravel (5.0) login and registration controllers with ApiClientAuth

exchange in app/Http/Controllers/Auth/AuthController.php original trait AuthenticatesAndRegistersUsers
for trait \Subscribo\ApiClientAuth\Traits\AuthenticatesAndRegistersUsersTrait
