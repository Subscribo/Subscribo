# Rest Client Package

Subscribo REST client for communicating with Subscribo API

## 1. Installation

Note: If used with package adding this dependency and registering this service provider for you (which is mostly the case),
steps 1.1 - 1.3 are not necessary; you can proceed to step [1.4 - Configuration](#14-configuration)

### 1.1 Add repository containing this package to your project's composer.json.

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key:

```json
    "subscribo/restclient": "@dev"
```

update composer

```sh
    composer update
```

### 1.3 For registering RestClientServiceProvider with Laravel (5.0)

add the following under 'provider' key in config/app.php file:

```php
    '\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider',
```

or (for conditional registration) add the following in bootstrap/app.php file:

```php
    if (class_exists('\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider')) {
        $app->register('\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
    }
```

### 1.4 Configuration

Set up your token_ring and if you are not using the defaults, set also host, protocol and/or uri_base
in subscribo/config/packages/restclient/default.yml (or default.php),
in configuration overriding it (e.g. subscribo/config/packages/restproxy/default.yml)
or in proper (used by your environment) .env file
(key names you can see in [docs/.env.example](docs/.env.example) of this package)

If you don't have your token ring, or if your token ring is not working properly
(you are receiving 401 Unathorized response from Subscribo API server)
contact your Subscribo Administrator.
