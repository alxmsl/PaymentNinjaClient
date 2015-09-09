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
 * Authenticate card script
 * @author alxmsl
 */

include __DIR__ . '/../../vendor/autoload.php';

use alxmsl\Cli\CommandPosix;
use alxmsl\Cli\Exception\RequiredOptionException;
use alxmsl\Cli\Option;
use alxmsl\PaymentNinja\Client;

$publicKey     = '';
$privateKey    = '';
$payerResponse = null;
$merchantData  = null;

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('data', 'd', 'ACS merchant data', Option::TYPE_STRING, true)
    , function($name, $value) use (&$merchantData) {
        $merchantData = (int) $value;
    });
$Command->appendParameter(new Option('private', 'r', 'project private key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$privateKey) {
        $privateKey = (string) $value;
    });
$Command->appendParameter(new Option('public', 'b', 'project public key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$publicKey) {
        $publicKey = (string) $value;
    });
$Command->appendParameter(new Option('response', 'r', 'ACS payer response', Option::TYPE_STRING, true)
    , function($name, $value) use (&$payerResponse) {
        $payerResponse = (int) $value;
    });

try {
    $Command->parse(true);

    $Client   = new Client($publicKey, $privateKey);
    $Response = $Client->cardAuthenticate($payerResponse, $merchantData);
    printf("%s\n", $Response);
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
