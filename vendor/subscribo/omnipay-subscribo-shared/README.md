# Package Subscribo OmnipaySubscriboShared 

###### is extending functionality of [Omnipay Common](https://github.com/thephpleague/omnipay-common) and providing shared functionality for some Omnipay drivers.

[![Build Status](https://travis-ci.org/Subscribo/omnipay-subscribo-shared.svg?branch=master)](https://travis-ci.org/Subscribo/omnipay-subscribo-shared)

- PSR-7 Message sending using Guzzle client currently (June 2015) used by Omnipay Common
- SimpleRestfulRequestTrait to help sending http requests
- RestfulResponseInterface, AbstractRestfulResponse and SimpleRestfulResponse to help making Omnipay responses out of http responses
- Widget interface and basic functionality
- Support for widget-based workflow (for APIs providing at some point displayable widget or parameter(s) to create it)
- Extended CreditCard class (additional attributes)
- Extended Item (additional attributes) and ItemBag classes
- Address parser helper

## Important note:

- This is a beta version.

## Installing

Add dependency on this package to your Omnipay driver composer.json.

You might need to add also dependencies to packages psr/http-message and development version of egeloen/http-adapter
both to your driver and your project, if you are using protected method HttpMessageSendingTrait::sendHttpMessage()
(and add recommendation for others to do so in your driver's README.md):

```json
    "require": {
        "subscribo/omnipay-subscribo-shared": "^0.3.4",
        "egeloen/http-adapter": "^0.8@dev",
        "psr/http-message": "^1.0"
    }
```

## Usage

You can extend abstract classes provided by this package,
use CreditCard and/or Item and ItemBag classes provided by this package,
use Widget base functionality provided by this package,
use trait SimpleRestfulRequestTrait for creating and sending http requests
or use just trait HttpMessageSendingTrait (for PSR-7 http message sending)
use helper GuzzleClientHelper (for httpClient logging using PSR-3 logger)
or use helper AddressParser (for parsing of first line of address)

## Contributing

For contribution guidelines see [CONTRIBUTING.md](CONTRIBUTING.md)

## License

Package Subscribo OmnipaySubscriboShared is published under [MIT License](http://opensource.org/licenses/MIT)
