<?php

namespace alxmsl\PaymentNinja\Response;

use alxmsl\PaymentNinja\InitializationInterface;


/**
 *
 * @author alxmsl
 */
final class UserResponse implements InitializationInterface {

    private $id = '';

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Response = json_decode($string);
        $Result = new UserResponse();
        $Result->id = (string) $Response->id;
        return $Result;
    }
}
