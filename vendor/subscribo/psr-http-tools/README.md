# Package Subscribo PsrHttpTools providing tools to be used with PSR-7:
- Request Factory
- Uri Factory
- Response Parser

## Installing

Add dependency on this package to your composer.json:
```json
    "require": {
        "subscribo/psr-http-tools": "~0.1"
    }
```

## Usage

### Factory

```php
    $request = \Subscribo\PsrHttpTools\Factories\RequestFactory::make($uri, $data);
```

### Parser

```php
    $data = \Subscribo\PsrHttpTools\Parsers\ResponseParser::extractDataFromResponse($response);
```
