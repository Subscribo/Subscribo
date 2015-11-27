# Client integration implementation for Thick Subscribo Client 

(Some important services (such as Authentication) are on client)

### Installation:

#### Require package in you project's `composer.json`:

```json
    {
        "require": {
            "subscribo/thick-client-integration": "@dev"
        }
    }
```

### Register `ThickClientIntegrationServiceProvider`

e.g. by adding into your `bootstrap/app.php`:

```php
    if (class_exists('\\Subscribo\\ThickClientIntegration\\Integration\\Laravel\\ThickClientIntegrationServiceProvider')) {
        $app->register('\\Subscribo\\ThickClientIntegration\\Integration\\Laravel\\ThickClientIntegrationServiceProvider');
    }
```
