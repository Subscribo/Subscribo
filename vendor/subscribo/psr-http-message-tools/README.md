# Package Subscribo PsrHttpMessageTools

Master branch (0.4) : [![Build Status](https://travis-ci.org/Subscribo/psr-http-message-tools.svg?branch=master)](https://travis-ci.org/Subscribo/psr-http-message-tools)

Branch 0.3 : [![Build Status](https://travis-ci.org/Subscribo/psr-http-message-tools.svg?branch=0.3)](https://travis-ci.org/Subscribo/psr-http-message-tools)

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
        "subscribo/psr-http-message-tools": "^0.4.0"
    }
```

For older version (0.3.x) (if you have PHP 5.4):

```json
    "require": {
        "subscribo/psr-http-message-tools": "^0.3.0"
    }
```

## Requirements

* Version 0.4 requires PHP 5.5 or higher and depends on package [zendframework/zend-diactoros](https://packagist.org/packages/zendframework/zend-diactoros)
* Version 0.3 requires PHP 5.4 or higher and depends on (currently abandoned) package [phly/http](https://packagist.org/packages/phly/http), which needs PHP 5.4.8 or higher
* Both versions depends on package [psr/http-message](https://packagist.org/packages/psr/http-message)

(Dependencies are installed by composer.)

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
