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

use alxmsl\PaymentNinja\Response\AuthenticateResponse;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class for ACS returned data handling
 * @author alxmsl
 */
final class Handler {
    /**
     * @var string public application key
     */
    private $publicKey = '';

    /**
     * @var string private application key
     */
    private $privateKey = '';

    /**
     * @param string $publicKey public application key
     * @param string $privateKey private application key
     */
    public function __construct($publicKey, $privateKey = '') {
        $this->publicKey  = (string) $publicKey;
        $this->privateKey = (string) $privateKey;
    }

    /**
     * Process ACS request
     * @param array $parameters POST request data
     * @param bool $needAuthenticate when needed authenticate request using Payment.Ninja API
     * @throws InvalidArgumentException when needed fields not found in parameters
     * @throws RuntimeException when signature is incorrect
     * @throws UnexpectedValueException when presented fields value are incorrect
     * @return null|AuthenticateResponse authenticate response when needed authenticate request using Payment.Ninja API
     */
    public function process(array $parameters, $needAuthenticate = true) {
        if (!isset($parameters['project'])) {
            throw new InvalidArgumentException('project value not defined');
        }
        if ($parameters['project'] == $this->publicKey) {
            throw new UnexpectedValueException('unexpected project value');
        }
        if (!isset($parameters['signature'])) {
            throw new InvalidArgumentException('signature value not defined');
        }
        if (!$this->checkSignature($parameters)) {
            throw new RuntimeException('signature verification failed');
        }

        if ($needAuthenticate) {
            return (new Client($this->publicKey, $this->privateKey))
                ->cardAuthenticate($parameters['PaRes'], $parameters['MD']);
        } else {
            return null;
        }
    }

    /**
     * Check POST parameters signature
     * @param array $parameters POST request data
     * @return bool check result
     */
    private function checkSignature(array $parameters) {
        $signature = $parameters['signature'];
        unset($parameters['signature']);
        sort($parameters, SORT_STRING);
        return ($signature == hash_hmac('sha256', join('|', $parameters), $this->privateKey));
    }
}
