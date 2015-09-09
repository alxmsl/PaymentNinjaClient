<?php
/**
 * Copyright 2015 Alexey Maslov <alexey.y.maslov@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Process payment script
 * @author alxmsl
 */

include __DIR__ . '/../../vendor/autoload.php';

use alxmsl\Cli\CommandPosix;
use alxmsl\Cli\Exception\RequiredOptionException;
use alxmsl\Cli\Option;
use alxmsl\PaymentNinja\Client;

$attributes        = [];
$currency          = '';
$description       = '';
$ip                = '';
$orderId           = '';
$price             = .0;
$privateKey        = '';
$publicKey         = '';
$recurring         = null;
$recurringInterval = null;
$recurringTrial    = null;
$remember          = null;
$url               = '';
$user              = '';
$verifyCard        = null;

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('attributes', 'a', 'custom attributes (JSON)', Option::TYPE_STRING)
    , function($name, $value) use (&$attributes) {
        $attributes = json_decode($value, true);
    });
$Command->appendParameter(new Option('currency', 'c', 'currency code (ISO 4217)', Option::TYPE_STRING, true)
    , function($name, $value) use (&$currency) {
        $currency = (string) $value;
    });
$Command->appendParameter(new Option('description', 'd', 'product description', Option::TYPE_STRING, true)
    , function($name, $value) use (&$description) {
        $description = (string) $value;
    });
$Command->appendParameter(new Option('interval', 'n'
        , 'automatic recurring interval in days (if not set or set to 0, then only manual recurring will be active)'
        , Option::TYPE_STRING)
    , function($name, $value) use (&$recurringInterval) {
        $recurringInterval = (int) $value;
    });
$Command->appendParameter(new Option('ip', 'i', 'user ip address', Option::TYPE_STRING, true)
    , function($name, $value) use (&$ip) {
        $ip = (string) $value;
    });
$Command->appendParameter(new Option('order', 'o', 'merchant\'s order ID that will be returned back in a callback'
        , Option::TYPE_STRING, true)
    , function($name, $value) use (&$orderId) {
        $orderId = (int) $value;
    });
$Command->appendParameter(new Option('price', 'p', 'price in real currency', Option::TYPE_STRING, true)
    , function($name, $value) use (&$price) {
        $price = (float) $value;
    });
$Command->appendParameter(new Option('private', 'r', 'project private key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$privateKey) {
        $privateKey = (string) $value;
    });
$Command->appendParameter(new Option('public', 'b', 'project public key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$publicKey) {
        $publicKey = (string) $value;
    });
$Command->appendParameter(new Option('recurring', 'e'
        , 'indicates whether a user wants to subscribe to recurring payments'
        , Option::TYPE_BOOLEAN)
    , function($name, $value) use (&$recurring) {
        $recurring = (bool) $value;
    });
$Command->appendParameter(new Option('remember', 'm', 'indicates whether a user wants to remember his credit card in
Merchant\'s service. If true, then permanentToken ​in response will contain token, that will be used for transaction
processing, instead of temporary token', Option::TYPE_BOOLEAN)
    , function($name, $value) use (&$remember) {
        $remember = (bool) $value;
    });
$Command->appendParameter(new Option('trial', 'f', 'Recurring trial period in days (first recurring payment will occur
after trial). Recurring trial will work only if recurring interval is set'
        , Option::TYPE_STRING)
    , function($name, $value) use (&$recurringTrial) {
        $recurringTrial = (int) $value;
    });
$Command->appendParameter(new Option('url', 'l', 'URL where 3DSecure service will return user after the authentication'
        , Option::TYPE_STRING, true)
    , function($name, $value) use (&$url) {
        $url = (string) $value;
    });
$Command->appendParameter(new Option('user', 'u', 'user identifier', Option::TYPE_STRING, true)
    , function($name, $value) use (&$user) {
        $user = (string) $value;
    });
$Command->appendParameter(new Option('verify', 'v', 'if set to 1, then transaction price will be set to 1 EUR, that will
be put on hold and then instantly returned', Option::TYPE_BOOLEAN)
    , function($name, $value) use (&$verifyCard) {
        $verifyCard = (bool) $value;
    });

try {
    $Command->parse(true);

    $Client   = new Client($publicKey, $privateKey);
    $Response = $Client->cardProcess($user, $orderId, $price, $currency, $description, $ip, $url, $remember, $verifyCard
        , $recurring, $recurringInterval, $recurringTrial, $attributes);
    printf("%s\n", $Response);
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
