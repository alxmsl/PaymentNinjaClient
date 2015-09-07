<?php

namespace alxmsl\PaymentNinja\Response;
use alxmsl\PaymentNinja\InitializationInterface;


/**
 *
 * @author alxmsl
 */
final class ProcessResponse implements InitializationInterface {

    private $id = '';

    private $success = false;

    private $Card = null;

    private $AccessControlServer = null;

    private $permanentToken = '';

    private $Recurring = null;

    /**
     * @inheritdoc
     */
    public static function initializeByString($string) {
        $Response = json_decode($string);
        $Result = new ProcessResponse();
        $Result->id = $Response->id;
        $Result->success = (bool) $Response->success;
        $Result->Card = CardResponse::initializeByObject($Response->card);
        $Result->AccessControlServer = AccessControlServerResponse::initializeByObject($Response->acs);
        $Result->permanentToken = (string) $Response->permanentToken;
        $Result->Recurring = RecurringResponse::initializeByObject($Response->recurring);
        return $Result;
    }
}
