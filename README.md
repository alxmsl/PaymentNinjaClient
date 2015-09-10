# PaymentNinjaClient

Powerful client for [Payment.Ninja REST API](https://payment.ninja/#about)

## Usage flow

1. User inputs and submits data
1. Merchant calls method [card/getToken](/bin/card/getToken) via JSONP or AJAX and receives a temporary token for 10
    minutes
1. Merchant submits credit card token to a server with other payment data
1. Merchant calls method [card/process](/bin/card/process) with credit card token
1. If `success` is `true` and ACS object is returned
1.1. Merchant POSTs ACS parameters `PaReq`, `MD`, `TermUrl` to aACS url in a browser
1.1. User inputs and submits his 3DSecure password
1.1. ACS POSTs parameters `PaRes`, `MD` back to merchant's ACS return url
1.1. Merchant calls method [card/authenticate](/bin/card/authenticate) passing `PaRes` and `MD` parameters
1.1. If `success` is `true` then merchant can provide a service to a user
1. If success is `true` and ACS object is not returned, then merchant can show a success page to user
1. If `remember` was passed, merchant will receive a `permanentToken` parameter with [card/process](/bin/card/process)
    or [card/authenticate](/bin/card/authenticate) method responses. You can use `permanentToken` without requiring a
    user to input the credit card data again
1. If `recurring` was passed, merchant will receive a recurring object, containing recurring frequency and ending date.
    Merchant can create new payments for a user calling the [card/processRecurring](/bin/card/processRecurring) method
1. If `verify_card` was passed, then transaction price will be set to €1, that will be put on hold and then instantly
    returned
1. `verify_card` can be effectively used with `recurring`, `recurring_interval`, `recurring_trial` or `remember`
    parameters as it checks the validity of a card via money hold/return
1. Merchant receives asynchronous callback with transaction details and can provide a￼service to a user if status is
    completed

## Tests

For completely tests running just call `phpunit` command

```
    PHPUnit 4.7.7 by Sebastian Bergmann and contributors.

    Runtime:	PHP 5.5.23 with Xdebug 2.3.2
    Configuration:	/Users/alxmsl/sources/PaymentNinjaClient.github/phpunit.xml.dist

    .................

    Time: 233 ms, Memory: 6.50Mb

    OK (17 tests, 90 assertions)
```

## License

Copyright 2015 Alexey Maslov <alexey.y.maslov@gmail.com>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
