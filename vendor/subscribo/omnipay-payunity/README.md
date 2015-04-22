# Omnipay: PayUnity

**PayUnity driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayUnity support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "subscribo/omnipay-payunity": "*@dev"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* PayUnity\COPYandPAY

Gateways in this package have following required options:

* securitySender
* transactionChannel
* userLogin
* userPwd

To get those please contact your PayUnity representative.

(Note: they are provided usually in the form 'SECURITY.SENDER' etc.)

Additionally these options could be specified:

* transactionMode
* testMode
* identificationShopperId
* identificationInvoiceId
* identificationBulkId (Note: not sure of having any effect at the moment)

For meaning and possible values of transactionMode ('TRANSACTION.MODE') see PayUnity documentation.

For meaning of testMode see general [Omnipay documentation](https://thephpleague.com/omnipay)

### Usage of gateway PayUnity\COPYandPAY

Gateway PayUnity\COPYandPAY supports these request-sending methods:

* purchase()
* completePurchase()

#### purchase()

Method purchase() expects an array with this key as its argument:

* amount

Additionally these keys could be specified:

* currency (e.g. EUR)
* brands
* returnUrl
* transactionId
* presentationUsage
* paymentMemo

Option brands could be an array or string with space separated list of (uppercase) brand identifiers, supported by COPYandPAY widget.
For supported brands see COPYandPAY documentation.

Option returnUrl should be an absolute url in your site, where user should be redirected after payment.

You need to provide brands and returnUrl either as part of purchase() argument, or when creating a widget later.

Method purchase() returns an instance of CopyAndPayPurchaseRequest having method send(), which in turn is sending the request and returning an instance of CopyAndPayPurchaseResponse having the following methods (additional to standard Omnipay RequestInterface methods and besides other helper and static methods):

* isTransactionToken()
* getTransactionToken()
* haveWidget()
* getWidget()
* getWidgetJavascript()
* getWidgetForm()

Method isSuccessful() always returns false, as the COPYandPAY workflow is as follows:

  1. using purchase() method you acquire transactionToken,
  2. then and you either manually, using static helpers
     or using CopyAndPayPurchaseResponse methods: getWidget()
     (or getWidgetJavascript() and getWidgetForm() if you want to have these parts separated)
     create the frontend widget and display it to customer
  3. and when customer fill and sends the widget,
  4. he is redirected to returnUrl provided,
  5. where you can finish/check the transaction (see below)

#### completePurchase()

Method completePurchase() could be called after customer had been redirected from widget (see above) back to your site.
It expects an array with key 'transactionToken' as a parameter,
however it could be invoked also with an empty array
and you can provide transaction token to returned instance of CopyAndPayCompletePurchaseRequest
via setTransactionToken($token) or fill(CopyAndPayPurchaseResponse $response) methods.

After transactionToken is provided to CopyAndPayCompletePurchaseRequest, you can call its send() method and receive CopyAndPayCompletePurchaseResponse, with following methods (additional to standard Omnipay RequestInterface methods):

* isWaiting() returns true when customer did not yet sent the widget form
* getIdentificationShortId()
* getIdentificationShopperId()

* getTransactionId() is alias for getIdentificationTransactionId()
* getTransactionReference() is alias for getIdentificationUniqueId()

### Example code

For example code see:

* [Purchase page](docs/example/purchase.php)
* [Complete purchase page](docs/example/complete_purchase.php)

### General instructions

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-dummy/issues),
or better yet, fork the library and submit a pull request.
