# RestProxy Package

Package Subscribo RestProxy is providing proxy functionality to access Subscribo API

## 1. Installation

1.1 Add repository containing this package to your project's composer.json

(note: you need to have access to this repository as well as to resources it points to)

1.2 Add to your project's composer.json dependency on this package under "require" key

```json
    "subscribo/RestProxy": "@dev"
```

1.3 If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider')) {
        $app->register('\\Subscribo\\RestProxy\\Integration\\Laravel\\RestProxyServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding this dependency and/or registering this service provider for you, respective steps might not be necessary.

1.4 You might need to configure this package in subscribo/config/packages/restproxy

as well as its dependency subscribo/config/packages/restclient

You can find example files within package src/config/default.yml

