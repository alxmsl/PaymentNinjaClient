<?php

namespace alxmsl\PaymentNinja\Response;
use alxmsl\PaymentNinja\InitializationInterface;


/**
 *
 * @author alxmsl
 */
final class SuccessResponse implements InitializationInterface {
    private $success = false;

    /**
     * @return bool
     */
    public function isSuccess() {
        return $this->success;
    }

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Response = json_decode($string);
        $Result = new SuccessResponse();
        $Result->success = (bool) $Response->success;
        return $Result;
    }
}
