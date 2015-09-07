<?php

namespace alxmsl\PaymentNinja\Response;
use alxmsl\PaymentNinja\InitializationInterface;


/**
 *
 * @author alxmsl
 */
final class TokenResponse implements InitializationInterface {

    private $token = '';

    private $expiresAt = 0;

    private $Card = null;

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getExpiresAt() {
        return $this->expiresAt;
    }

    /**
     * @return null
     */
    public function getCard() {
        return $this->Card;
    }

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Response = json_decode($string);
        $Result = new TokenResponse();
        $Result->token = (string) $Response->token;
        $Result->expiresAt = strtotime($Response->expiresAt);
        $Result->Card = CardResponse::initializeByObject($Response->card);
        return $Result;
    }
}
