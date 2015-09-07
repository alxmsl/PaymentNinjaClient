<?php

namespace alxmsl\PaymentNinja\Response;

use alxmsl\PaymentNinja\ObjectInitializedInterface;
use stdClass;

/**
 *
 * @author alxmsl
 */
final class AccessControlServerResponse implements ObjectInitializedInterface {

    private $url = '';

    private $parmeters = '';

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getParmeters() {
        return $this->parmeters;
    }

    public static function initializeByObject(stdClass $Object) {
        $Result = new AccessControlServerResponse();
        $Result->url = (string) $Object->url;
        $Result->parmeters = (string) $Object->parameters;
        return $Result;
    }
}
