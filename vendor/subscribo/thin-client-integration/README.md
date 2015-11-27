# Client integration implementation for Thin Subscribo Client (all or almost all services are on the API server)

### Installation:

#### Require package in you project's `composer.json`:

```json
    {
        "require": {
            "subscribo/thin-client-integration": "@dev"
        }
    }
```

### Register `ThinClientIntegrationServiceProvider`

e.g. by adding into your `bootstrap/app.php`:

```php
    if (class_exists('\\Subscribo\\ThinClientIntegration\\Integration\\Laravel\\ThinClientIntegrationServiceProvider')) {
        $app->register('\\Subscribo\\ThinClientIntegration\\Integration\\Laravel\\ThinClientIntegrationServiceProvider');
    }
```
