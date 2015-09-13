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
        'user/resolve'          => HttpRequest::METHOD_POST,
        'user/changeRecurring'  => HttpRequest::METHOD_POST,
        'user/cancelRecurring'  => HttpRequest::METHOD_POST,
        'card/getToken'         => HttpRequest::METHOD_GET,
        'card/process'          => HttpRequest::METHOD_POST,
        'card/authenticate'     => HttpRequest::METHOD_POST,
        'card/processRecurring' => HttpRequest::METHOD_POST,
    ];

    /**
     * @param string $method API method name
     * @param Closure $ResponseBuilder
     * @param array $parameters method call parameters
     */
    public function __construct($method, Closure $ResponseBuilder, array $parameters = []) {
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
                'project' => (string) $publicKey,
            ], $this->parameters);
            $this->parameters['signature'] = $this->signParameters($this->parameters, (string) $privateKey);
        } else {
            throw new LogicException('parameters already signed');
        }
    }

    /**
     * Execute API method
     * @codeCoverageIgnore
     * @return AbstractResponse response instance
     * @throws ErrorException if there is an API error
     */
    public function execute() {
        $Request = $this->createRequest();
        try {
            return $this->ResponseBuilder->__invoke($Request->send());
        } catch (HttpClientErrorCodeException $Ex) {
            throw ErrorException::initializeByString($Ex->getMessage());
        }
    }

    /**
     * Create HTTP request instance fr this API call
     * @return HttpRequest HTTP request instance
     */
    private function createRequest() {
        $HttpRequest = new HttpRequest();
        $HttpRequest->setTransport(HttpRequest::TRANSPORT_CURL);
        $HttpRequest->setUrl(self::ENDPOINT_URI)
            ->setConnectTimeout(1)
            ->setTimeout(10)
            ->addUrlField($this->method)
            ->setMethod(self::$methodMap[$this->method]);
        $this->appendParameters($HttpRequest);
        return $HttpRequest;
    }

    /**
     * Append parameters to HTTP request
     * @param HttpRequest $HttpRequest HTTP request instance
     */
    private function appendParameters(HttpRequest $HttpRequest) {
        switch ($HttpRequest->getMethod()) {
            case HttpRequest::METHOD_GET:
                foreach ($this->parameters as $field => $value) {
                    $HttpRequest->addGetField($field, $value);
                }
                break;
            case HttpRequest::METHOD_POST:
                $HttpRequest->setPostData($this->parameters);
                break;
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
