# Environment Package

Package for getting current environment

## 1. Installation

1.1 Add the following to your project's composer.json:

1.1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.1.2 Dependency under "require" key

```json
    "subscribo/environment": "@dev"
```

1.2. If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\Environment\\Integration\\Laravel\\EnvironmentServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\Environment\\Integration\\Laravel\\EnvironmentServiceProvider')) {
        $app->register('\\Subscribo\\Environment\\Integration\\Laravel\\EnvironmentServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

If used with package adding this dependency and registering this service provider for you, this step (1.2) is not necessary.

