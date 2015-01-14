# Config Package

Config package for reading configuration

## 1. Installation

1.1 Add repository containing this package to your project's composer.json

(note: you need to have access to this repository as well as to resources it points to)

1.2 Add to your project's composer.json dependency under "require" key
(note: do not add trailing comma if it is the last item listed)

```json
    "subscribo/config": "@dev",
```

1.3 You might need to add (to the same place as in 1.2) also dependencies suggested in this package's composer.json, especially:

```json
    "subscribo/environment": "@dev",
    "subscribo/serviceprovider": "@dev",
```

1.4 If you are using Laravel (5.0), you might want to add

```php
    '\\Subscribo\\Config\\Integration\\Laravel\\ConfigServiceProvider',
```

under 'provider' key in config/app.php file.

or

```php
    if (class_exists('\\Subscribo\\Config\\Integration\\Laravel\\ConfigServiceProvider')) {
        $app->register('\\Subscribo\\Config\\Integration\\Laravel\\ConfigServiceProvider');
    }
```

in bootstrap/app.php for conditional registration

Note: If used with package adding these dependencies and/or registering this service provider for you, respective steps might not be necessary.

