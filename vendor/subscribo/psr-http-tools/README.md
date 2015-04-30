[![Build Status](https://travis-ci.org/Subscribo/psr-http-tools.svg?branch=master)](https://travis-ci.org/Subscribo/psr-http-tools)

# Package Subscribo PsrHttpTools
## providing following tools to be used with PSR-7 compliant classes:
- Request Factory
- Uri Factory
- Response Parser

## Important notes:

- This is a beta version.
- This is an auxiliary package with limited functionality

## Installing

Add dependency on this package to your composer.json:
```json
    "require": {
        "subscribo/psr-http-tools": "^0.2.0"
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

## Contributing

For contribution guidelines see [CONTRIBUTING.md](CONTRIBUTING.md)

## License

Package Subscribo PsrHttpTools is published under [MIT License](http://opensource.org/licenses/MIT)
