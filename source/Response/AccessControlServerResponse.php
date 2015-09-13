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
 * ACS data class
 * @author alxmsl
 */
final class AccessControlServerResponse implements ObjectInitializedInterface {
    /**
     * @var string URL where a user should be redirected with the secure3dParameters
     */
    private $url = '';

    /**
     * @var AccessControlServerParametersResponse parameters that should be passed to secure3dUrl​as-is using the
     *  HTTP POST method
     */
    private $parameters = null;

    /**
     * @return string URL where a user should be redirected with the secure3dParameters
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return AccessControlServerParametersResponse parameters that should be passed to secure3dUrl​as-is using the HTTP POST method
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     * @return AccessControlServerResponse instance with ACS data
     */
    public static function initializeByObject(stdClass $Object) {
        $Result             = new AccessControlServerResponse();
        $Result->url        = (string) $Object->url;
        $Result->parameters = AccessControlServerParametersResponse::initializeByObject($Object->parameters);
        return $Result;
    }

    /**
     * @inheritdoc
     */
    public function __toString() {
        $format = <<<'EOD'
        url:       %s
        parameters
            MD:    %s
            PaReq: %s
            Terms: %s
        query:     %s
EOD;
        return sprintf($format
            , $this->getUrl()
            , $this->getParameters()->getMerchantData()
            , $this->getParameters()->getPaymentAuthorizationRequest()
            , $this->getParameters()->getTermsUrl()
            , $this->getParameters()->getQuery());
    }
}
