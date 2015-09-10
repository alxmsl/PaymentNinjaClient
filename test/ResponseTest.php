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

use alxmsl\PaymentNinja\Response\AuthenticateResponse;
use alxmsl\PaymentNinja\Response\ProcessRecurringResponse;
use alxmsl\PaymentNinja\Response\ProcessResponse;
use alxmsl\PaymentNinja\Response\SuccessResponse;
use alxmsl\PaymentNinja\Response\TokenResponse;
use alxmsl\PaymentNinja\Response\UserResponse;
use PHPUnit_Framework_TestCase;

/**
 * Tests for API response classes
 * @author alxmsl
 */
final class ResponseTest extends PHPUnit_Framework_TestCase {
    public function testUserResponse() {
        $Response = UserResponse::initializeByString('{"id": 12345}');
        $this->assertEquals('12345', $Response->getId());
        $this->assertEquals('user\'s data
    id: 12345', (string) $Response);
    }

    public function testSuccessResponse() {
        $Response = SuccessResponse::initializeByString('{"success": true}');
        $this->assertTrue($Response->isSuccess());
        $this->assertEquals('response:
    success: true', (string) $Response);

        $Response = SuccessResponse::initializeByString('{"success": false}');
        $this->assertFalse($Response->isSuccess());
        $this->assertEquals('response:
    success: false', (string) $Response);
    }

    public function testTokenResponse() {
        $Response = TokenResponse::initializeByString('{
"id": "11fe4b4d430eccc4ca59b8df31bc5d161b4d54020082362a7d389486f5349066",
"expiresAt": "2015-05-22T11:21:37+03:00",
"card": {
"lastFour": "4242",
"mask": "************4242",
"type": "visa",
"expirationMonth": "4",
"expirationYear": "2020" }
}');
        $this->assertEquals('11fe4b4d430eccc4ca59b8df31bc5d161b4d54020082362a7d389486f5349066', $Response->getId());
        $this->assertEquals(1432282897, $Response->getExpiresAt());
        $this->assertEquals('4242', $Response->getCard()->getLastFour());
        $this->assertEquals('************4242', $Response->getCard()->getMask());
        $this->assertEquals(4, $Response->getCard()->getExpirationMonth());
        $this->assertEquals(2020, $Response->getCard()->getExpirationYear());
        $this->assertEquals('token\'s data
    id:        11fe4b4d430eccc4ca59b8df31bc5d161b4d54020082362a7d389486f5349066
    expiresAt: 2015-05-22 11:21:37
    card
        four:       4242
        mask:       ************4242
        type:       visa
        exp. month: 4
        exp. year:  2020', (string) $Response);
    }

    public function testProcessResponse() {
        $Response = ProcessResponse::initializeByString('{
"id": 1326123574311498453, "success": true,
"card": {
"lastFour": "4242",
"mask": "************4242",
"type": "visa",
"expirationMonth": "4",
"expirationYear": "2020" },
"acs": {
"url": "https://example.com/ACS", "parameters": {
"MD": "eyJ0cmFuc2Fj...",
"PaReq": "eJxdUWF...",
"TermUrl": "http://example.com/return"
} },
"permanentToken": "9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca", "recurring": {
"frequency": 1,
"endsAt": "2015-10-22T11:49:23+03:00" }
}');
        $this->assertEquals('1326123574311498453', $Response->getId());
        $this->assertTrue($Response->isSuccess());
        $this->assertEquals('4242', $Response->getCard()->getLastFour());
        $this->assertEquals('************4242', $Response->getCard()->getMask());
        $this->assertEquals(4, $Response->getCard()->getExpirationMonth());
        $this->assertEquals(2020, $Response->getCard()->getExpirationYear());
        $this->assertEquals('https://example.com/ACS', $Response->getAccessControlServer()->getUrl());
        $this->assertEquals('eyJ0cmFuc2Fj...', $Response->getAccessControlServer()->getParameters()->getMerchantData());
        $this->assertEquals('eJxdUWF...', $Response->getAccessControlServer()->getParameters()
            ->getPaymentAuthorizationRequest());
        $this->assertEquals('http://example.com/return', $Response->getAccessControlServer()->getParameters()
            ->getTermsUrl());
        $this->assertEquals('9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca'
            , $Response->getPermanentToken());
        $this->assertEquals(1, $Response->getRecurring()->getFrequency());
        $this->assertEquals(1445503763, $Response->getRecurring()->getEndsAt());
        $this->assertEquals('process result
    id:             1326123574311498453
    success:        true
    permanentToken: 9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca
    card
        four:       4242
        mask:       ************4242
        type:       visa
        exp. month: 4
        exp. year:  2020
    acs
        url:        https://example.com/ACS
        parameters
            MD:     eyJ0cmFuc2Fj...
            PaReq:  eJxdUWF...
            Terms:  http://example.com/return
    recurring
        frequency:  1
        endsAt:     2015-10-22 11:49:23', (string) $Response);
    }

    public function testProcessRecurringResponse() {
        $Response = ProcessRecurringResponse::initializeByString('{
"id": 1326123574311498453, "success": true,
"card": {
"lastFour": "4242",
"type": "visa",
"mask": "************4242",
"expirationMonth": "4",
"expirationYear": "2020" } }');
        $this->assertEquals('1326123574311498453', $Response->getId());
        $this->assertTrue($Response->isSuccess());
        $this->assertEquals('4242', $Response->getCard()->getLastFour());
        $this->assertEquals('************4242', $Response->getCard()->getMask());
        $this->assertEquals(4, $Response->getCard()->getExpirationMonth());
        $this->assertEquals(2020, $Response->getCard()->getExpirationYear());
        $this->assertEquals('process recurring result
    id:             1326123574311498453
    success:        true
    card
        four:       4242
        mask:       ************4242
        type:       visa
        exp. month: 4
        exp. year:  2020', (string) $Response);
    }

    public function testAuthenticateResponse() {
        $Response = AuthenticateResponse::initializeByString('{
"id": 1326123574311498453, "success": true,
"card": {
"lastFour": "4242",
"mask": "************4242",
"type": "visa",
"expirationMonth": "4",
"expirationYear": "2020" },
"permanentToken": "9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca",
"recurring": {
"frequency": 1,
"endsAt": "2015-10-22T11:49:23+03:00"
} }');
        $this->assertEquals('1326123574311498453', $Response->getId());
        $this->assertTrue($Response->isSuccess());
        $this->assertEquals('4242', $Response->getCard()->getLastFour());
        $this->assertEquals('************4242', $Response->getCard()->getMask());
        $this->assertEquals(4, $Response->getCard()->getExpirationMonth());
        $this->assertEquals(2020, $Response->getCard()->getExpirationYear());
        $this->assertEquals('9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca'
            , $Response->getPermanentToken());
        $this->assertEquals(1, $Response->getRecurring()->getFrequency());
        $this->assertEquals(1445503763, $Response->getRecurring()->getEndsAt());
        $this->assertEquals('authenticate result
    id:             1326123574311498453
    success:        true
    permanentToken: 9a083895d07ca58f6e5505bd19ed35ca9a083895d07ca58f6e5505bd19ed35ca
    card
        four:       4242
        mask:       ************4242
        type:       visa
        exp. month: 4
        exp. year:  2020
    recurring
        frequency:  1
        endsAt:     2015-10-22 11:49:23', (string) $Response);
    }

}
