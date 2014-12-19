# Model Base Package

Model Base provides Eloquent based abstract class to make models extending it as well as a factory for listing and generating models

## Installation

1. Add the following to your project's composer.json:

1.1 Repository containing this package (note: you need to have access to this repository as well as to resources it points to)

1.2 Dependency under "require" key

```json
    "subscribo/modelbase": "@dev"
```

1.3 If you are using Laravel (4.2), you might want to add

```php
    '\\Subscribo\\ModelBase\\Support\\Laravel\\ModelBaseServiceProvider',
```

under 'provider' key in app/config/app.php file.

and

```json
    "subscribo/serviceprovider": "@dev"
```

under "require" or "require-dev" keys in your composer.json

If used with package adding this dependency and registering this service provider for you, this step (1.3) is not necessary.

