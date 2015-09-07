<?php

namespace alxmsl\PaymentNinja\Response;

use alxmsl\PaymentNinja\ObjectInitializedInterface;
use stdClass;

/**
 *
 * @author alxmsl
 */
final class RecurringResponse implements ObjectInitializedInterface {

    private $frequency = false;

    private $endsAt = '';

    public static function initializeByObject(stdClass $Object) {
        $Result = new RecurringResponse();
        $Result->frequency = (bool) $Object->frequency;
        $Result->endsAt = (string) $Object->ends;
        return $Result;
    }
}
