# RestProxy Package

Package Subscribo RestProxy is providing proxy functionality to access Subscribo API

## 1. Installation

1.1 Add the following to your project's composer.json:

1.1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.1.2 Dependency under "require" key

```json
    "subscribo/RestProxy": "@dev"
```

1.2. If you are using Laravel (5.0), you might want to add

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

If used with package adding this dependency and registering this service provider for you, this step (1.2) is not necessary.

1.3 You might need to configure this package in subscribo/config/packages/restproxy

as well as its dependency subscribo/config/packages/restclient

You can find example files within package src/config/default.yml

