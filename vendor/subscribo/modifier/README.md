# Modifier Package

Modifier allows modifying multiple values using set of predefined rules

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" key

```json
    "subscribo/modifier": "@dev"
```

1.3 If you are using Laravel (4.2), you might want to add

```php
    '\\Subscribo\\Modifier\\Support\\Laravel\\ModifierServiceProvider',
```

under 'provider' key in app/config/app.php file.

If used with package registering this service provider for you, this step is not necessary.

