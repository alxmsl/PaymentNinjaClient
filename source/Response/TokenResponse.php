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
 * Class for card's token data
 * @author alxmsl
 */
final class TokenResponse implements InitializationInterface {
    /**
     * @var string temporary, 10-minutes card token
     */
    private $token = '';

    /**
     * @var int expiration timestamp
     */
    private $expiresAt = 0;

    /**
     * @var null|CardResponse instance with safely card data
     */
    private $Card = null;

    /**
     * @return string temporary, 10-minutes card token
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return int expiration timestamp
     */
    public function getExpiresAt() {
        return $this->expiresAt;
    }

    /**
     * @return null|CardResponse instance with safely card data
     */
    public function getCard() {
        return $this->Card;
    }

    /**
     * @inheritdoc
     * @return TokenResponse card's token instance
     */
    public static function initializeByString($string) {
        $Response          = json_decode($string);
        $Result            = new TokenResponse();
        $Result->token     = (string) $Response->token;
        $Result->expiresAt = strtotime($Response->expiresAt);
        $Result->Card      = CardResponse::initializeByObject($Response->card);
        return $Result;
    }
}
