# Package Subscribo OmnipaySubscriboShared providing shared functionality for some Omnipay drivers

## Installation:

Add dependency on this package to your Omnipay driver composer.json:
```json
    "require": {
        "subscribo/omnipay-subscribo-shared": "~0.1"
    }
```

## Usage

You can extend abstract classes provided by this package
or use trait HttpMessageSendingTrait (for PSR-7 http message sending)
or helper GuzzleClientHelper (for httpClient logging using PSR-3 logger)
