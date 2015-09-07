<?php

namespace alxmsl\PaymentNinja\Response;

use alxmsl\PaymentNinja\ObjectInitializedInterface;
use stdClass;

/**
 *
 * @author alxmsl
 */
final class CardResponse implements ObjectInitializedInterface {

    private $lastFour = 0;

    private $mask = '';

    private $expirationMonth = 0;

    private $expirationYear = 0;

    /**
     * @return int
     */
    public function getLastFour() {
        return $this->lastFour;
    }

    /**
     * @return string
     */
    public function getMask() {
        return $this->mask;
    }

    /**
     * @return int
     */
    public function getExpirationMonth() {
        return $this->expirationMonth;
    }

    /**
     * @return int
     */
    public function getExpirationYear() {
        return $this->expirationYear;
    }

    public static function initializeByObject(stdClass $Object) {
        $Result = new CardResponse();
        $Result->lastFour = (int) $Object->lastFour;
        $Result->mask = (string) $Object->mask;
        $Result->expirationMonth = (int) $Object->expirationMonth;
        $Result->expirationYear = (int) $Object->expirationYear;
        return $Result;
    }
}
