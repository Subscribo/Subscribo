# Package Subscribo PsrHttpTools providing tools to be used with PSR-7:
- Request Factory
- Uri Factory

## Installing:

Add dependency on this package to your composer.json:
```json
    "require": {
        "subscribo/psr-http-tools": "@dev"
    }
```

## Usage:

```php
    $request = \Subscribo\PsrHttpTools\Factories\RequestFactory::make($uri, $data);
```

