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
 * User resolve script
 * @author alxmsl
 */

include __DIR__ . '/../../vendor/autoload.php';

use alxmsl\Cli\CommandPosix;
use alxmsl\Cli\Exception\RequiredOptionException;
use alxmsl\Cli\Option;
use alxmsl\PaymentNinja\Client;

$publicKey   = '';
$privateKey  = '';
$user        = '';
$email       = '';
$ip          = '';
$displayName = null;
$locale      = null;
$phone       = null;

$Command = new CommandPosix();
$Command->appendHelpParameter('show help');
$Command->appendParameter(new Option('display', 'd', 'user\'s display name', Option::TYPE_STRING)
    , function($name, $value) use (&$displayName) {
        $displayName = (string) $value;
    });
$Command->appendParameter(new Option('email', 'e', 'user email address', Option::TYPE_STRING, true)
    , function($name, $value) use (&$email) {
        $email = (string) $value;
    });
$Command->appendParameter(new Option('ip', 'i', 'user ip address', Option::TYPE_STRING, true)
    , function($name, $value) use (&$ip) {
        $ip = (string) $value;
    });
$Command->appendParameter(new Option('locale', 'l', 'user\'s locale (ISO 639-1)', Option::TYPE_STRING)
    , function($name, $value) use (&$locale) {
        $locale = (string) $value;
    });
$Command->appendParameter(new Option('phone', 'p', 'user\'s phone number', Option::TYPE_STRING)
    , function($name, $value) use (&$phone) {
        $phone = (string) $value;
    });
$Command->appendParameter(new Option('private', 'r', 'project private key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$privateKey) {
        $privateKey = (string) $value;
    });
$Command->appendParameter(new Option('public', 'b', 'project public key', Option::TYPE_STRING, true)
    , function($name, $value) use (&$publicKey) {
        $publicKey = (string) $value;
    });
$Command->appendParameter(new Option('user', 'u', 'user unique unchangeable identifier', Option::TYPE_STRING, true)
    , function($name, $value) use (&$user) {
        $user = (string) $value;
    });

try {
    $Command->parse(true);

    $Client   = new Client($publicKey, $privateKey);
    $Response = $Client->userResolve($user, $email, $ip, $displayName, $locale, $phone)->execute();
    printf("%s\n", $Response);
} catch (RequiredOptionException $Ex) {
    $Command->displayHelp();
}
