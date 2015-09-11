# PaymentNinjaClient

[![License](https://poser.pugx.org/alxmsl/paymentninjaclient/license)](https://packagist.org/packages/alxmsl/paymentninjaclient)
[![Latest Stable Version](https://poser.pugx.org/alxmsl/paymentninjaclient/version)](https://packagist.org/packages/alxmsl/paymentninjaclient)
[![Total Downloads](https://poser.pugx.org/alxmsl/paymentninjaclient/downloads)](https://packagist.org/packages/alxmsl/paymentninjaclient)

[![Build Status](https://travis-ci.org/alxmsl/PaymentNinjaClient.svg)](https://travis-ci.org/alxmsl/PaymentNinjaClient)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alxmsl/PaymentNinjaClient/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alxmsl/PaymentNinjaClient/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/alxmsl/PaymentNinjaClient/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alxmsl/PaymentNinjaClient/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/55f2ef8bd4d204001e00012a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55f2ef8bd4d204001e00012a)

Powerful client for [Payment.Ninja REST API](https://payment.ninja/#about)

## Usage flow

1. User inputs and submits data
1. Merchant calls method [card/getToken](/bin/card/getToken.php) via JSONP or AJAX and receives a temporary token for 10
    minutes
1. Merchant submits credit card token to a server with other payment data
1. Merchant calls method [card/process](/bin/card/process.php) with credit card token
1. If `success` is `true` and ACS object is returned
    1. Merchant POSTs ACS parameters `PaReq`, `MD`, `TermUrl` to aACS url in a browser
    1. User inputs and submits his 3DSecure password
    1. ACS POSTs parameters `PaRes`, `MD` back to merchant's ACS return url
    1. Merchant calls method [card/authenticate](/bin/card/authenticate.php) passing `PaRes` and `MD` parameters
    1. If `success` is `true` then merchant can provide a service to a user
1. If success is `true` and ACS object is not returned, then merchant can show a success page to user
1. If `remember` was passed, merchant will receive a `permanentToken` parameter with
    [card/process](/bin/card/process.php) or [card/authenticate](/bin/card/authenticate.php) method responses. You can
    use `permanentToken` without requiring a user to input the credit card data again
1. If `recurring` was passed, merchant will receive a recurring object, containing recurring frequency and ending date.
    Merchant can create new payments for a user calling the [card/processRecurring](/bin/card/processRecurring.php)
    method
1. If `verify_card` was passed, then transaction price will be set to €1, that will be put on hold and then instantly
    returned
1. `verify_card` can be effectively used with `recurring`, `recurring_interval`, `recurring_trial` or `remember`
    parameters as it checks the validity of a card via money hold/return
1. Merchant receives asynchronous callback with transaction details and can provide a￼service to a user if status is
    completed

## Installation

For simplified usage all what you need is require packet via composer

```
    $ composer require alxmsl/paymentninjaclient
```

In third-party projects, require packet in your `composer.json`

```
    "alxmsl/paymentninjaclient": "*"
```

...and update composer: `composer update`

## Usages

Firstly, create client instance with public and private key, that was provided in your account

```
    use alxmsl\PaymentNinja\Client;
    $Client = new Client('<public key>', '<private key>');
```

Now you can create request for REST API methods

- `user/resolve` via `Client::userResolve()`
- `user/changeRecurring` via `Client::userChangeRecurring()`
- `user/cancelRecurring` via `Client::userCancelRecurring()`
- `card/getToken` via `Client::cardGetToken()`
- `card/process` via `Client::cardProcess()`
- `card/authenticate` via `Client::cardAuthenticate()`
- `card/processRecurring` via `Client::cardProcessRecurring()`

For request execution you should call `Request::execute()` method. For example below code

```
    use alxmsl\PaymentNinja\Client;
    $Client = new Client('pU811cKE4', 'Pr1v4tEKEy');
    $R = $Client->userResolve('aaa@aaa.ru', 'aaa@aaa.ru', '127.0.0.1')->execute();
    var_dump($R);
```

...going to follow output

```
    class alxmsl\PaymentNinja\Response\UserResponse#8 (1) {
      private $id =>
      string(5) "46919"
    }
```

## Console usage

Surely, you can use simple CLI utilities for calling REST API methods

- `user/resolve` via `./bin/user/resolve`
- `user/changeRecurring` via `./bin/user/changeRecurring`
- `user/cancelRecurring` via `./bin/user/cancelRecurring`
- `card/getToken` via `./bin/card/getToken`
- `card/process` via `./bin/card/process`
- `card/authenticate` via `./bin/card/authenticate`
- `card/processRecurring` via `./bin/card/processRecurring`

So, user resolving example must be present as

```
    $ php ./bin/user/resolve.php -b='pU811cKE4' -r='Pr1v4tEKEy' -e='aaa@aaa.ru' -u='aaa@aaa.ru' -i='127.0.0.1'
    user's data
        id: 46919
```

Each utility supports their quick help page

```
$ php bin/card/getToken.php  --help
Using: /usr/local/bin/php bin/card/getToken.php [-h|--help] [-c|--callback] -m|--month -n|--number -r|--private -b|--public -s|--security -y|--year
-h, --help  - show help
-c, --callback  - callback JSONP function name
-m, --month  - expiration month
-n, --number  - card number
-r, --private  - project private key
-b, --public  - project public key
-s, --security  - card security code
-y, --year  - expiration year
```

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
