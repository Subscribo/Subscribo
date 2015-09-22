# Package Subscribo PsrHttpMessageTools

[![Build Status](https://travis-ci.org/Subscribo/psr-http-message-tools.svg?branch=master)](https://travis-ci.org/Subscribo/psr-http-message-tools)

**Package Subscribo PsrHttpMessageTools is providing following tools to be used with PSR-7 compliant classes:**
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
        "subscribo/psr-http-message-tools": "^0.4.3"
    }
```

## Requirements

* PHP 5.4 or higher
* [zendframework/zend-diactoros](https://packagist.org/packages/zendframework/zend-diactoros)
* [psr/http-message](https://packagist.org/packages/psr/http-message)

(Package dependencies are installed by composer.)

## Usage

### Factory

```php
    $request = \Subscribo\PsrHttpMessageTools\Factories\RequestFactory::make($uri, $data);
```

### Parser

```php
    $data = \Subscribo\PsrHttpMessageTools\Parsers\ResponseParser::extractDataFromResponse($response);
```

## Contributing

For contribution guidelines see [CONTRIBUTING.md](CONTRIBUTING.md)

## License

Package Subscribo PsrHttpMessageTools is published under [MIT License](http://opensource.org/licenses/MIT)
