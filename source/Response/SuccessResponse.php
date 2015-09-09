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

use alxmsl\PaymentNinja\InitializationInterface;

/**
 * Class for simple responses from API
 * @author alxmsl
 */
final class SuccessResponse implements InitializationInterface {
    /**
     * @var bool request processing result
     */
    private $success = false;

    /**
     * @return bool request processing result
     */
    public function isSuccess() {
        return $this->success;
    }

    /**
     * @inheritdoc
     * @return SuccessResponse simple response instance
     */
    public static function initializeByString($string) {
        $Response        = json_decode($string);
        $Result          = new SuccessResponse();
        $Result->success = (bool) $Response->success;
        return $Result;
    }

    /**
     * @inheritdoc
     */
    public function __toString() {
        $format = <<<'EOD'
response:
    success: %s
EOD;
        return sprintf($format, $this->isSuccess() ? 'true' : 'false');
    }
}
