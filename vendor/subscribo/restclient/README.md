# Rest Client Package

Subscribo REST client for communicating with Subscribo API

## 1. Installation

### 1.1 Add repository containing this package to your project's composer.json.

(Note: you need to have access to this repository as well as to resources it points to)

### 1.2 Add to your project's composer.json dependency on this package under "require" key:

```json
    "subscribo/restclient": "@dev"
```

### 1.3 If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider')) {
        $app->register('\\Subscribo\\RestClient\\Integration\\Laravel\\RestClientServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps are not necessary.

### 1.4 Configure your token_ring and if needed also host, uri_base, protocol
in subscribo/config/packages/restcommon/default.yml (or default.php), in configuration overriding it
(e.g. subscribo/config/packages/restproxy/default.yml) or in proper .env file
