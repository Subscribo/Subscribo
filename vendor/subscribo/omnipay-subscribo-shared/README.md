# Package Subscribo OmnipaySubscriboShared 

###### is extending functionality of [Omnipay Common](https://github.com/thephpleague/omnipay-common) and providing shared functionality for some Omnipay drivers.

[![Build Status](https://travis-ci.org/Subscribo/omnipay-subscribo-shared.svg?branch=master)](https://travis-ci.org/Subscribo/omnipay-subscribo-shared)

- PSR-7 Message sending using Guzzle client currently (April 2015) used by Omnipay Common
- Added support for widget-based workflow
- Extended CreditCard class 

## Important note:

- This is a beta version.

## Installing

Add dependency on this package as well to your Omnipay driver composer.json.
You might need to add dependency to development version of egeloen/http-adapter package both to your driver and your project
(and add recommendation for others to do so in your driver's README.md):

```json
    "require": {
        "subscribo/omnipay-subscribo-shared": "^0.2",
        "egeloen/http-adapter": "^0.8@dev"
    }
```

## Usage

You can extend abstract classes provided by this package,
use CreditCard class provided by this package
use trait HttpMessageSendingTrait (for PSR-7 http message sending)
or use helper GuzzleClientHelper (for httpClient logging using PSR-3 logger)

## Contributing

For contribution guidelines see [CONTRIBUTING.md](CONTRIBUTING.md)

## License

Package Subscribo PsrHttpTools is published under [MIT License](http://opensource.org/licenses/MIT)
