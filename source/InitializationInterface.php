<?php
/*
 * Copyright 2015 Topface, LLC <alexey.y.maslov@topface.com>
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

namespace alxmsl\PaymentNinja;

/**
 * Interface for self-initialization objects
 * @author alxmsl
 * @date 2/9/13
 */
interface InitializationInterface {
    /**
     * Initialization method
     * @param string $string data for object initialization
     * @return InitializationInterface initialized object
     */
    public static function initializeByString($string);
}
