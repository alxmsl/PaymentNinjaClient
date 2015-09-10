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

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs');
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'card_token'     => 'tkn',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'signature'      => '5c98d9316b1dafac6d33ad0605101fe4b4c07fba6791e37772ead929e874573c',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'card_token'     => 'tkn',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'signature'      => '23b76427e7880036d133a8df3f8eac5cd580650b1ab9458abc624f135685c21a',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'card_token'     => 'tkn',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'verify_card'    => true,
            'signature'      => 'b5ee9c6c78c341cf33be6995e29c017a841ecee06aa81b56ef3231474419b13a',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true);
        $this->assertEquals([
            'project'        => '111',
            'user'           => '444',
            'card_token'     => 'tkn',
            'order_id'       => '555',
            'price'          => 7.,
            'currency'       => 'RUB',
            'description'    => 'Lorem ipsum',
            'ip'             => '127.0.0.1',
            'acs_return_url' => 'http://alxmsl.ru/acs',
            'remember'       => false,
            'verify_card'    => true,
            'recurring'      => true,
            'signature'      => 'c5b7613bcf1962a635ad924a2f02983ff9ba9310bdfe01caa4e22844ec29c701',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'card_token'     => 'tkn',
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
            'signature'          => 'eec09dfe0c2e937065fb5d4df8b65749ee6ca86efa311b0d9e378ef20b298b32',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5, 1);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'card_token'     => 'tkn',
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
            'signature'          => '86b3f52404f1ca93178575e1c95d413cc9d6b03eff642676fd234436e510dbd4',
        ], $this->getRequestParametersProperty($Request));

        $Request = $Client->cardProcess('444', 'tkn', '555', 7, 'RUB', 'Lorem ipsum', '127.0.0.1', 'http://alxmsl.ru/acs'
            , false, true, true, 5, 1, [
                'test1' => 'some_attr',
                'test2' => 5,
            ]);
        $this->assertEquals([
            'project'            => '111',
            'user'               => '444',
            'card_token'     => 'tkn',
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
            'signature'          => '5e53aba4507f2729764f7e72ffe5d68898e61b5bcb6f7cf2860188cd104f999c',
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
