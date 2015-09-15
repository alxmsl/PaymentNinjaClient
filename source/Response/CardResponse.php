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

use alxmsl\PaymentNinja\ObjectInitializedInterface;
use stdClass;

/**
 * Class for card's data
 * @author alxmsl
 */
final class CardResponse implements ObjectInitializedInterface {
    /**
     * @var int last four digits of credit card number
     */
    private $lastFour = 0;

    /**
     * @var string credit card's number mask
     */
    private $mask = '';

    /**
     * @var int credit card's expiration month
     */
    private $expirationMonth = 0;

    /**
     * @var int credit card's expiration year
     */
    private $expirationYear = 0;

    /**
     * @var string card type
     */
    private $type = '';

    /**
     * @return int last four digits of credit card number
     */
    public function getLastFour() {
        return $this->lastFour;
    }

    /**
     * @return string credit card's number mask
     */
    public function getMask() {
        return $this->mask;
    }

    /**
     * @return int credit card's expiration month
     */
    public function getExpirationMonth() {
        return $this->expirationMonth;
    }

    /**
     * @return int credit card's expiration year
     */
    public function getExpirationYear() {
        return $this->expirationYear;
    }

    /**
     * @return string card's type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @inheritdoc
     * @return CardResponse instance with safely card data
     */
    public static function initializeByObject(stdClass $Object) {
        $Result                  = new CardResponse();
        $Result->lastFour        = (int) $Object->lastFour;
        $Result->mask            = (string) $Object->mask;
        $Result->expirationMonth = (int) $Object->expirationMonth;
        $Result->expirationYear  = (int) $Object->expirationYear;
        $Result->type            = (string) $Object->type;
        return $Result;
    }

    /**
     * @inheritdoc
     */
    public function __toString() {
        $format = <<<'EOD'
        four:       %s
        mask:       %s
        type:       %s
        exp. month: %s
        exp. year:  %s
EOD;
        return sprintf($format
            , $this->getLastFour()
            , $this->getMask()
            , $this->getType()
            , $this->getExpirationMonth()
            , $this->getExpirationYear());
    }
}
