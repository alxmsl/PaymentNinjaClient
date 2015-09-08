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

namespace alxmsl\PaymentNinja\Response;

use alxmsl\PaymentNinja\InitializationInterface;

/**
 * Class for card's authentication data
 * @author alxmsl
 */
final class AuthenticateResponse implements InitializationInterface {
    /**
     * @var string transaction identifier
     */
    private $id = '';

    /**
     * @var bool request processing result
     */
    private $success = false;

    /**
     * @var null|CardResponse instance with safely card data
     */
    private $Card = null;

    /**
     * @var string card's permanent token
     */
    private $permanentToken = '';

    /**
     * @var null|RecurringResponse instance with recurring payments data
     */
    private $Recurring = null;

    /**
     * @return string transaction identifier
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return boolean request processing result
     */
    public function isSuccess() {
        return $this->success;
    }

    /**
     * @return null|CardResponse instance with safely card data
     */
    public function getCard() {
        return $this->Card;
    }

    /**
     * @return string card's permanent token
     */
    public function getPermanentToken() {
        return $this->permanentToken;
    }

    /**
     * @return null|RecurringResponse instance with recurring payments data
     */
    public function getRecurring() {
        return $this->Recurring;
    }

    /**
     * @inheritdoc
     * @return AuthenticateResponse instance, that describes card authentication data
     */
    public static function initializeByString($string) {
        $Response               = json_decode($string);
        $Result                 = new AuthenticateResponse();
        $Result->id             = $Response->id;
        $Result->success        = (bool) $Response->success;
        $Result->Card           = CardResponse::initializeByObject($Response->card);
        $Result->permanentToken = (string) $Response->permanentToken;
        $Result->Recurring      = RecurringResponse::initializeByObject($Response->recurring);
        return $Result;
    }
}
