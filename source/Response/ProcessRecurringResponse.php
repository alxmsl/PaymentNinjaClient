<?php

namespace alxmsl\PaymentNinja\Response;
use alxmsl\PaymentNinja\InitializationInterface;


/**
 *
 * @author alxmsl
 */
final class ProcessRecurringResponse implements InitializationInterface {

    private $id = '';

    private $success = false;

    private $Card = null;

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Response = json_decode($string);
        $Result = new ProcessRecurringResponse();
        $Result->id = $Response->id;
        $Result->success = (bool) $Response->success;
        $Result->Card = CardResponse::initializeByObject($Response->card);
        return $Result;
    }
}
