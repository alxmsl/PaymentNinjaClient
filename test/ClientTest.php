<?php
/*
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
 */

namespace alxmsl\Test\PaymentNinja;

use alxmsl\PaymentNinja\Client;
use alxmsl\PaymentNinja\Request;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Tests class for Payment.Ninja REST API client
 * @author alxmsl
 */
final class ClientTest extends PHPUnit_Framework_TestCase {
    public function testInitialization() {
        $Client = new Client('PUBLICKEY', 'PRIVATEKEY');
        $Class = new ReflectionClass(Client::class);
        $PublicKeyProperty = $Class->getProperty('publicKey');
        $PublicKeyProperty->setAccessible(true);
        $PrivateKeyProperty = $Class->getProperty('privateKey');
        $PrivateKeyProperty->setAccessible(true);

        $this->assertEquals('PUBLICKEY', $PublicKeyProperty->getValue($Client));
        $this->assertEquals('PRIVATEKEY', $PrivateKeyProperty->getValue($Client));
    }

    public function testUserResolveRequest() {
        $Client = new Client(111, 222);

        $Request = $Client->userResolve('333', 'aaa@aaa.ru', '127.0.0.1');
        $this->assertEquals([
            'project'    => '111',
            'identifier' => '333',
            'email'      => 'aaa@aaa.ru',
            'ip'         => '127.0.0.1',
            'signature'  => 'bdf79a4f0b5113097efdbc3764cb3e53538a2b20034dbe95f69be66441101f8d',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userResolve('333', 'aaa@aaa.ru', '127.0.0.1', 'Alexey Maslov');
        $this->assertEquals([
            'project'      => '111',
            'identifier'   => '333',
            'email'        => 'aaa@aaa.ru',
            'ip'           => '127.0.0.1',
            'display_name' => 'Alexey Maslov',
            'signature'    => '95bd008f5e65770d48ad2c6fc41b2484987894524bf9cff872260a22cb7fbed1',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userResolve('333', 'aaa@aaa.ru', '127.0.0.1', 'Alexey Maslov', 'ru');
        $this->assertEquals([
            'project'      => '111',
            'identifier'   => '333',
            'email'        => 'aaa@aaa.ru',
            'ip'           => '127.0.0.1',
            'display_name' => 'Alexey Maslov',
            'locale'       => 'ru',
            'signature'    => 'ecdf063b7e9294f095ffb408e9ea95f417f15a882d3e2ffb5f11836d3c3ba3a5',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userResolve('333', 'aaa@aaa.ru', '127.0.0.1', 'Alexey Maslov', 'ru', '+12345678');
        $this->assertEquals([
            'project'      => '111',
            'identifier'   => '333',
            'email'        => 'aaa@aaa.ru',
            'ip'           => '127.0.0.1',
            'display_name' => 'Alexey Maslov',
            'locale'       => 'ru',
            'phone'        => '+12345678',
            'signature'    => '737c903a6f72d8e088a324daa1e572dd7bd1b30e06f61482ea0dbbc390c7c4dd',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testUserChangeRecurring() {
        $Client = new Client(111, 222);

        $Request = $Client->userChangeRecurring('333');
        $this->assertEquals([
            'project'   => '111',
            'user'      => '333',
            'signature' => 'f68c15c701dd75d0d20436a7b5a4254a5ea8b772e4bd6ddb288269a1522780f3',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userChangeRecurring('333', 7);
        $this->assertEquals([
            'project'   => '111',
            'user'      => '333',
            'interval'  => 7,
            'signature' => '68705471ac675e3c7fa8a2d48d4fdcf41a6841430b515ae9ee0e28cfc5bd3c73',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userChangeRecurring('333', 7, 5.55);
        $this->assertEquals([
            'project'   => '111',
            'user'      => '333',
            'interval'  => 7,
            'price'     => 5.55,
            'signature' => 'f06580ca71a20f108b1089169ef2ce381aa7e73aadbbd2e9a0de5f4b263a4db0',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->userChangeRecurring('333', 7, 5.55, 'USD');
        $this->assertEquals([
            'project'   => '111',
            'user'      => '333',
            'interval'  => 7,
            'price'     => 5.55,
            'currency'  => 'USD',
            'signature' => '073d7e8aadbf245ae7c9478c161c4deb34d43d7f6493452cbbe8f5631b389c42',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testUserCancelRecurring() {
        $Client = new Client(111, 222);

        $Request = $Client->userCancelRecurring('333');
        $this->assertEquals([
            'project'   => '111',
            'user'      => '333',
            'signature' => 'f68c15c701dd75d0d20436a7b5a4254a5ea8b772e4bd6ddb288269a1522780f3',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testCardGetTokenTest() {
        $Client = new Client(111, 222);

        $Request = $Client->cardGetToken('4242424242424242', 7, 2020, 123);
        $this->assertEquals([
            'project'          => '111',
            'number'           => '4242424242424242',
            'expiration_month' => 7,
            'expiration_year'  => 2020,
            'security_code'    => '123',
            'signature'        => '811eb1e7394ce4c293b8f8bda0aeb5894c9b725db9c8510c7c993d40e568287f',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardGetToken('4242424242424242', 7, 2020, 123, 'workflow');
        $this->assertEquals([
            'project'          => '111',
            'number'           => '4242424242424242',
            'expiration_month' => 7,
            'expiration_year'  => 2020,
            'security_code'    => '123',
            'callback'         => 'workflow',
            'signature'        => '0b1647268bfd6f3d5810c111ff5b09e06e6c54e0d6e2c3bd19489a0506407100',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testCardProcess() {
        $Client = new Client(111, 222);

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs');
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'signature'      => '9eb01e2246287d6796a0faada03296c4b1943509004ad9c6ce7665bb62d80050',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'signature'      => '0dca14bc5f6d0b041c4497f04e5d138213e991a4c3f6633cdb2dc35452aeb092',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'verify_card'    => true,
            'signature'      => 'b2ec1fc3f85d784d31e9c584e8f7587beccf443c2eee74f6bae0f97fc3dd2250',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'verify_card'    => true,
            'recurring'      => true,
            'signature'      => '03ba936e04d40a6d6312dc36ae5909e21b8cf56baabcb9b5076baab770e088fc',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'order_id'           => '555',
            'price'              => 7.,
            'currency'           => 'RUB',
            'description'        => 'Lorem ipsum',
            'ip'                 => '127.0.0.1',
            'acs_return_url'     => 'http://alxmsl.ru/acs',
            'remember'           => false,
            'verify_card'        => true,
            'recurring'          => true,
            'recurring_interval' => 5,
            'signature'          => '82d7615ba945deaab1062c544429d41ef686122c95aff929e8665649efbd3f63',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5, 1);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'order_id'           => '555',
            'price'              => 7.,
            'currency'           => 'RUB',
            'description'        => 'Lorem ipsum',
            'ip'                 => '127.0.0.1',
            'acs_return_url'     => 'http://alxmsl.ru/acs',
            'remember'           => false,
            'verify_card'        => true,
            'recurring'          => true,
            'recurring_interval' => 5,
            'recurring_trial'    => 1,
            'signature'          => '2c272b5941f1497bc67197a8ae5ed712658f42d98b8c96f14d728b89fcc1a4f7',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5, 1, [
                'test1' => 'some_attr',
                'test2' => 5,
            ]);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'order_id'           => '555',
            'price'              => 7.,
            'currency'           => 'RUB',
            'description'        => 'Lorem ipsum',
            'ip'                 => '127.0.0.1',
            'acs_return_url'     => 'http://alxmsl.ru/acs',
            'remember'           => false,
            'verify_card'        => true,
            'recurring'          => true,
            'recurring_interval' => 5,
            'recurring_trial'    => 1,
            'attr_test1'         => 'some_attr',
            'attr_test2'         => 5,
            'signature'          => '552c155a50219319f699bcd3c499da6f4dfe02beb1667dfab4e83a928e407c2d',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testCardAuthenticate() {
        $Client = new Client(111, 222);

        $Request = $Client->cardAuthenticate('payer authentication response', 'merchant data');
        $this->assertEquals([
            'project' => '111',
            'PaRes' => 'payer authentication response',
            'MD' => 'merchant data',
            'signature' => 'a3cef80d780843f81083cf3a9b15eadd4236c6f86a55c1a25b58c43850ba6154',
        ], $this->getRequestParametersProperty($Request));
    }

    public function testCardProcessRecurring() {
        $Client = new Client(111, 222);

        $Request = $Client->cardProcessRecurring('999', 5.4, 'EUR');
        $this->assertEquals([
            'project'   => '111',
            'user'      => '999',
            'price'     => 5.4,
            'currency'  => 'EUR',
            'signature' => '7cefe246acf59297fd3673a06d890acdeb6c7e27848e1531328b3060250e466b',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcessRecurring('999', 5.4, 'EUR', '777');
        $this->assertEquals([
            'project'   => '111',
            'user'      => '999',
            'price'     => 5.4,
            'currency'  => 'EUR',
            'order_id'  => '777',
            'signature' => 'c19bdae6d3041ca4b3f462a1338a5e3c46455152b4a218272b7c3cc2f077767e',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcessRecurring('999', 5.4, 'EUR', '777', 'Lorem recurring');
        $this->assertEquals([
            'project'     => '111',
            'user'        => '999',
            'price'       => 5.4,
            'currency'    => 'EUR',
            'order_id'    => '777',
            'description' => 'Lorem recurring',
            'signature'   => 'ebeb906d67d3031f3a81eecf0d655b2b822be7c2dee07aea2eeb94a0d512f44d',
        ], $this->getRequestParametersProperty($Request));
    }

    /**
     * Get request parameters for request instance
     * @param Request $Request request instance
     * @return array request parameters array
     */
    private function getRequestParametersProperty(Request $Request) {
        $Class              = new ReflectionClass(Request::class);
        $PropertyParameters = $Class->getProperty('parameters');
        $PropertyParameters->setAccessible(true);
        return $PropertyParameters->getValue($Request);
    }
}
