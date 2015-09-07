<?php

namespace alxmsl\PaymentNinja;

use alxmsl\Network\Http\Request as HttpRequest;
use LogicException;

/**
 *
 * @author alxmsl
 */
final class Request {
    /**
     * Payment.Ninja API endpoint
     */
    const ENDPOINT_URI = 'https://api.payment.ninja/v1';
    
    private $method = '';

    private $parameters = [];

    private static $methodMap = [
        'user/resolve'  => HttpRequest::METHOD_POST,
        'card/getToken' => HttpRequest::METHOD_GET,
    ];

    public function __construct($method, array $parameters = []) {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function sign($publicKey, $secretKey) {
        if (!array_key_exists('signature', $this->parameters)) {
            $this->parameters = array_merge([
                'project' => $publicKey,
            ], $this->parameters);
            $this->parameters['signature'] = $this->signParameters($this->parameters, $secretKey);
        } else {
            throw new LogicException('parameters already signed');
        }
    }

    public function execute() {
        $Request = new HttpRequest();
        $Request->setTransport(HttpRequest::TRANSPORT_CURL);
        $Request->setUrl(self::ENDPOINT_URI)
            ->setConnectTimeout(1)
            ->setTimeout(3)
            ->addUrlField($this->method);
        $Request->setMethod(self::$methodMap[$this->method]);
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
        return $Request->send();
    }
    
    private function signParameters(array $parameters, $secretKey) {
        sort($parameters, SORT_STRING);
        return hash_hmac('sha256', join('|', $parameters), $secretKey);
    }
}
