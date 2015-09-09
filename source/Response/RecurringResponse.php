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
 * Class for transaction recurrent data
 * @author alxmsl
 */
final class RecurringResponse implements ObjectInitializedInterface {
    /**
     * @var int minimal number of days between recurring payments
     */
    private $frequency = 0;

    /**
     * @var string last date timestamp when recurring payments will work
     */
    private $endsAt = '';

    /**
     * @return int minimal number of days between recurring payments
     */
    public function getFrequency() {
        return $this->frequency;
    }

    /**
     * @return string last date timestamp when recurring payments will work
     */
    public function getEndsAt() {
        return $this->endsAt;
    }

    public static function initializeByObject(stdClass $Object) {
        $Result            = new RecurringResponse();
        $Result->frequency = (int) $Object->frequency;
        $Result->endsAt    = strtotime($Object->ends);
        return $Result;
    }
}
