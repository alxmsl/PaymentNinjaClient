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

use alxmsl\Network\Http\Request as HttpRequest;
use alxmsl\PaymentNinja\Request;
use LogicException;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

/**
 * Test for API request class
 * @author alxmsl
 */
final class RequestTest extends PHPUnit_Framework_TestCase {
    public function testInitialization() {
        $Class = new ReflectionClass(Request::class);
        $MethodProperty = $Class->getProperty('method');
        $MethodProperty->setAccessible(true);
        $ParametersProperty = $Class->getProperty('parameters');
        $ParametersProperty->setAccessible(true);
        $BuilderFunction = $Class->getProperty('ResponseBuilder');
        $BuilderFunction->setAccessible(true);

        $Request = new Request('some/method', function() {});
        $this->assertEquals('some/method', $MethodProperty->getValue($Request));
        $this->assertCount(0, $ParametersProperty->getValue($Request));

        $Request = new Request('some/method', function() {}, [
            'param1' => 'value1'
        ]);
        $this->assertEquals('some/method', $MethodProperty->getValue($Request));
        $this->assertCount(1, $ParametersProperty->getValue($Request));
        $this->assertEquals([
            'param1' => 'value1'
        ], $ParametersProperty->getValue($Request));

        $Function = function() {};
        $Request = new Request('some/method', $Function);
        $this->assertEquals('some/method', $MethodProperty->getValue($Request));
        $this->assertCount(0, $ParametersProperty->getValue($Request));
        $this->assertEquals($Function, $BuilderFunction->getValue($Request));
    }

    public function testSign() {
        $Class = new ReflectionClass(Request::class);
        $ParametersProperty = $Class->getProperty('parameters');
        $ParametersProperty->setAccessible(true);

        $Function = function() {};
        $Request = new Request('some/method', $Function, [
            'a' => 'value1',
            'b' => 'value2',
        ]);
        $Request->sign(111, 222);
        $this->assertEquals([
            'project'   => '111',
            'a'         => 'value1',
            'b'         => 'value2',
            'signature' => '54d72211dc40f096b27f5bc3895d2a62a0ff7a21bdbc0fe7c62f5d2dfb1a0b6a'
        ], $ParametersProperty->getValue($Request));

        $Request = new Request('some/method', $Function, [
            'b' => 'value2',
            'a' => 'value1',
        ]);
        $Request->sign(111, 222);
        $this->assertEquals([
            'project'   => '111',
            'a'         => 'value1',
            'b'         => 'value2',
            'signature' => '54d72211dc40f096b27f5bc3895d2a62a0ff7a21bdbc0fe7c62f5d2dfb1a0b6a'
        ], $ParametersProperty->getValue($Request));

        try {
            $Request->sign(333, 444);
            $this->fail();
        } catch (LogicException $Ex) {}
    }

    public function testCreateRequest() {
        $Class = new ReflectionClass(Request::class);
        $CreateRequestMethod = $Class->getMethod('createRequest');
        $CreateRequestMethod->setAccessible(true);

        $Request = new Request('user/resolve', function() {}, [
            'param1' => 'value1',
            'param2' => 'value2',
        ]);
        /** @var HttpRequest $HttpRequest */
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertInstanceOf(HttpRequest::class, $HttpRequest);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());
        $this->assertEquals([
            'user/resolve' => '',
        ], $HttpRequest->getUrlData());
        $this->assertEquals([
            'param1' => 'value1',
            'param2' => 'value2',
        ], $HttpRequest->getPostData());

        $Request = new Request('user/resolve', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('user/changeRecurring', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('user/cancelRecurring', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('card/getToken', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_GET, $HttpRequest->getMethod());

        $Request = new Request('card/process', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('card/authenticate', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('card/processRecurring', function() {});
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals(HttpRequest::METHOD_POST, $HttpRequest->getMethod());

        $Request = new Request('card/getToken', function() {}, [
            'param1' => 'value1',
            'param2' => 'value2',
        ]);
        $HttpRequest = $CreateRequestMethod->invoke($Request);
        $this->assertEquals([
            'param1' => 'value1',
            'param2' => 'value2',
        ], $HttpRequest->getGetData());
    }
}
