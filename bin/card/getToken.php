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
 * Get token for credit card script
 * @author alxmsl
 */

include __DIR__ . '/../../vendor/autoload.php';

use alxmsl\Cli\CommandPosix;
use alxmsl\Cli\Exception\RequiredOptionException;
use alxmsl\Cli\Option;
use alxmsl\PaymentNinja\Client;

$expirationMonth = 0;
$expirationYear  = 0;
$number          = '';
$securityCode    = '';
$callback        = null;

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('callback', 'c', 'callback JSONP function name', Option::TYPE_STRING)
    , function($name, $value) use (&$callback) {
        $callback = (string) $value;
    });
$Command->appendParameter(new Option('month', 'm', 'expiration month', Option::TYPE_STRING, true)
    , function($name, $value) use (&$expirationMonth) {
        $expirationMonth = (int) $value;
    });
$Command->appendParameter(new Option('number', 'n', 'card number', Option::TYPE_STRING, true)
    , function($name, $value) use (&$number) {
        $number = (string) $value;
    });
$Command->appendParameter(new Option('public', 'b', 'project public key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$publicKey) {
        $publicKey = (string) $value;
    });
$Command->appendParameter(new Option('security', 's', 'card security code', Option::TYPE_STRING, true)
    , function($name, $value) use (&$securityCode) {
        $securityCode = (string) $value;
    });
$Command->appendParameter(new Option('year', 'y', 'expiration year', Option::TYPE_STRING, true)
    , function($name, $value) use (&$expirationYear) {
        $expirationYear = (int) $value;
    });

try {
    $Command->parse(true);

    $Client   = new Client($publicKey);
    $Response = $Client->cardGetToken($number, $expirationMonth, $expirationYear, $securityCode, $callback)->execute();
    printf("%s\n", $Response);
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
