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

namespace alxmsl\PaymentNinja;

use alxmsl\Network\Exception\HttpClientErrorCodeException;
use alxmsl\Network\Http\Request as HttpRequest;
use alxmsl\PaymentNinja\Error\ErrorException;
use alxmsl\PaymentNinja\Response\AbstractResponse;
use Closure;
use LogicException;

/**
 * Class for API requests
 * @author alxmsl
 */
final class Request {
    /**
     * Payment.Ninja API endpoint
     */
    const ENDPOINT_URI = 'https://api.payment.ninja/v1';

    /**
     * @var string API method name
     */
    private $method = '';

    /**
     * @var array method call parameters
     */
    private $parameters = [];

    /**
     * @var Closure|null response builder function
     */
    private $ResponseBuilder = null;

    /**
     * @var array map that store relations between API and HTTP methods for requests
     */
    private static $methodMap = [
        'card/getToken' => HttpRequest::METHOD_GET,
    ];

    /**
     * @param string $method API method name
     * @param array $parameters method call parameters
     * @param Closure $ResponseBuilder
     */
    public function __construct($method, array $parameters = [], Closure $ResponseBuilder) {
        $this->method          = (string) $method;
        $this->parameters      = $parameters;
        $this->ResponseBuilder = $ResponseBuilder;
    }

    /**
     * Sign request
     * @param string $publicKey public application key
     * @param string $privateKey private application key
     * @throws LogicException when parameters already signed
     */
    public function sign($publicKey, $privateKey) {
        if (!array_key_exists('signature', $this->parameters)) {
            $this->parameters = array_merge([
                'project' => $publicKey,
            ], $this->parameters);
            $this->parameters['signature'] = $this->signParameters($this->parameters, $privateKey);
        } else {
            throw new LogicException('parameters already signed');
        }
    }

    /**
     * Execute API method
     * @return AbstractResponse response instance
     * @throws ErrorException if there is an API error
     */
    public function execute() {
        $Request = new HttpRequest();
        $Request->setTransport(HttpRequest::TRANSPORT_CURL);
        $Request->setUrl(self::ENDPOINT_URI)
            ->setConnectTimeout(1)
            ->setTimeout(3)
            ->addUrlField($this->method);

        // Uses HTTP method from the map for known API functions. By default uses POST
        $httpMethod = array_key_exists($this->method, self::$methodMap)
            ? self::$methodMap[$this->method]
            : HttpRequest::METHOD_POST;
        $Request->setMethod($httpMethod);

        switch ($Request->getMethod()) {
            case HttpRequest::METHOD_GET:
                foreach ($this->parameters as $field => $value) {
                    $Request->addGetField($field, $value);
                }
                break;
            case HttpRequest::METHOD_POST:
                $Request->setPostData($this->parameters);
                break;
        }

        try {
            return $this->ResponseBuilder->__invoke($Request->send());
        } catch (HttpClientErrorCodeException $Ex) {
            throw ErrorException::initializeByString($Ex->getMessage());
        }
    }

    /**
     * Calculates signature for incoming parameters
     * @param array $parameters parameters
     * @param string $privateKey private application key
     * @return string signature
     */
    private function signParameters(array $parameters, $privateKey) {
        sort($parameters, SORT_STRING);
        return hash_hmac('sha256', join('|', $parameters), $privateKey);
    }
}
