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

namespace alxmsl\PaymentNinja;

use alxmsl\Network\Exception\HttpClientErrorCodeException;
use alxmsl\PaymentNinja\Error\ErrorException;
use alxmsl\PaymentNinja\Response\AuthenticateResponse;
use alxmsl\PaymentNinja\Response\ProcessRecurringResponse;
use alxmsl\PaymentNinja\Response\ProcessResponse;
use alxmsl\PaymentNinja\Response\SuccessResponse;
use alxmsl\PaymentNinja\Response\TokenResponse;
use alxmsl\PaymentNinja\Response\UserResponse;

/**
 * Payment.Ninja REST API client
 * @author alxmsl
 */
final class Client {
    /**
     * @var string public application key
     */
    private $publicKey = '';

    /**
     * @var string private application key
     */
    private $privateKey = '';

    /**
     * @param string $publicKey public application key
     * @param string $privateKey private application key
     */
    public function __construct($publicKey, $privateKey) {
        $this->publicKey  = (string) $publicKey;
        $this->privateKey = (string) $privateKey;
    }

    /**
     * REST API method user/resolve implementation
     * @param string $id user's unique, unchangeable identifier
     * @param string $email user's email address
     * @param string $ip user's IP address
     * @param null|string $displayName user's display name, if present
     * @param null|string $locale user's locale as ISO 639-1
     * @param null|string $phone user's phne number if present
     * @return UserResponse resolved user instance
     * @throws ErrorException if there is an API error
     */
    public function userResolve($id, $email, $ip, $displayName = null, $locale = null, $phone = null) {
        $parameters = [
            'identifier' => (string) $id,
            'email'      => (string) $email,
            'ip'         => (string) $ip,
        ];
        if (!is_null($displayName)) {
            $parameters['display_name'] = (string) $displayName;
        }
        if (!is_null($locale)) {
            $parameters['locale'] = (string) $locale;
        }
        if (!is_null($phone)) {
            $parameters['phone'] = (string) $phone;
        }
        return UserResponse::initializeByString($this->call('user/resolve', $parameters));
    }

    /**
     * REST API method user/changeRecurring implementation
     * @param string $userId user's identifier
     * @param null|int $interval automatic recurring interval in days (if set 0, then only manual recurring will remain active)
     * @param null|float $price recurring price
     * @param null|int $currency recurring currency as ISO 4217
     * @return SuccessResponse method response instance
     * @throws ErrorException if there is an API error
     */
    public function userChangeRecurring($userId, $interval = null, $price = null, $currency = null) {
        $parameters = [
            'user' => (string) $userId,
        ];
        if (!is_null($interval)) {
            $parameters['interval'] = (int) $interval;
        }
        if (!is_null($price)) {
            $parameters['price'] = (float) $price;
        }
        if (!is_null($currency)) {
            $parameters['currency'] = (string) $currency;
        }
        return SuccessResponse::initializeByString($this->call('user/changeRecurring', $parameters));
    }

    /**
     * REST API method user/cancelRecurring implementation
     * @param string $userId user's identifier
     * @return SuccessResponse method response instance
     * @throws ErrorException if there is an API error
     */
    public function userCancelRecurring($userId) {
        return SuccessResponse::initializeByString($this->call('user/cancelRecurring', [
            'user' => (string) $userId,
        ]));
    }

    /**
     * REST API method card/getToken implementation
     * @param string $number credit card's number
     * @param int $expirationMonth credit card's expiration month, without leading zero
     * @param int $expirationYear credit card's expiration year (4 digits)
     * @param string $securityCode credit card's security code: CVC, CVV2
     * @param null|string $callback callback function name for JSONP
     * @return TokenResponse card's token instance
     * @throws ErrorException if there is an API error
     */
    public function cardGetToken($number, $expirationMonth, $expirationYear, $securityCode, $callback = null) {
        $parameters = [
            'number'           => (string) $number,
            'expiration_month' => (int) $expirationMonth,
            'expiration_year'  => (int) $expirationYear,
            'security_code'    => (string) $securityCode,
        ];
        if (!is_null($callback)) {
            $parameters['callback'] = (string) $callback;
        }
        return TokenResponse::initializeByString($this->call('card/getToken', $parameters));
    }

    /**
     * REST API method card/process implementation
     * @param string $userId user's identifier
     * @param string $orderId merchant's order ID that will be returned back in a callback
     * @param float $price price in real currency
     * @param string $currency currency code as ISO 4217
     * @param string $description product description
     * @param string $ip user's IP address
     * @param string $acsReturnUrl URL where 3DSecure service will return user after the authentication
     * @param null|bool $remember indicates whether a user wants to remember his credit card in Merchant's service.
     *  If true , then permanenToken ​in response will contain token, that will be used for transaction processing,
     *  instead of temporary token
     * @param null|bool $verifyCard if true, then transaction price will be set to 1 EUR, that will be put on hold and
     *  then instantly returned
     * @param null|bool $recurring indicates whether a user wants to subscribe to recurring payments
     * @param null|int $recurringInterval automatic recurring interval in days (if not set or set to 0, then only manual
     *  recurring will be active)
     * @param null|int $recurringTrial Recurring trial period in days (first recurring payment will occur after trial).
     *  Recurring trial will work only if recurring interval is set
     * @param array $attributes custom attributes data
     * @return ProcessResponse instance, that describes payment process result
     * @throws ErrorException if there is an API error
     */
    public function cardProcess($userId, $orderId, $price, $currency, $description, $ip, $acsReturnUrl,
                                $remember = null, $verifyCard = null, $recurring = null, $recurringInterval = null,
                                $recurringTrial = null, array $attributes = []) {

        $parameters = [
            'user'           => (string) $userId,
            'order_id'       => (string) $orderId,
            'price'          => (float) $price,
            'currency'       => (string) $currency,
            'description'    => (string) $description,
            'ip'             => (string) $ip,
            'acs_return_url' => (string) $acsReturnUrl,
        ];
        if (!is_null($remember)) {
            $parameters['remember'] = (bool) $remember;
        }
        if (!is_null($verifyCard)) {
            $parameters['verify_card'] = (bool) $verifyCard;
        }
        if (!is_null($recurring)) {
            $parameters['recurring'] = (bool) $recurring;
        }
        if (!is_null($recurringInterval)) {
            $parameters['recurring_interval'] = (int) $recurringInterval;
        }
        if (!is_null($recurringTrial)) {
            $parameters['recurring_trial'] = (int) $recurringTrial;
        }
        foreach ($attributes as $attributeName => $attributeValue) {
            $parameters[sprintf('attr_%s', $attributeName)] = $attributeValue;
        }
        return ProcessResponse::initializeByString($this->call('card/process', $parameters));
    }

    /**
     * REST API method card/authenticate implementation
     * @param string $payerResponse payer authentication response. Returned from ACS to a​cs_return_url
     * @param string $merchantData merchant data. Returned from ACS to acs_return_url
     * @return AuthenticateResponse instance, that describes card authentication data
     * @throws ErrorException if there is an API error
     */
    public function cardAuthenticate($payerResponse, $merchantData) {
        return AuthenticateResponse::initializeByString($this->call('card/authenticate', [
            'PaRes' => (string) $payerResponse,
            'MD'    => (string) $merchantData,
        ]));
    }

    /**
     * REST API method card/processRecurring implementation
     * @param string $userId user's identifier
     * @param float $price price in real currency
     * @param string $currency currency code as ISO 4217
     * @param null|string $orderId merchant's order ID that will be returned back in a callback
     * @param null|string $description product description
     * @return ProcessRecurringResponse instance, that describes payment recurring process result
     * @throws ErrorException if there is an API error
     */
    public function cardProcessRecurring($userId, $price, $currency, $orderId = null, $description = null) {
        $parameters = [
            'user'     => (string) $userId,
            'price'    => (float) $price,
            'currency' => (string) $currency,
        ];
        if (!is_null($orderId)) {
            $parameters['order_id'] = (string) $orderId;
        }
        if (!is_null($description)) {
            $parameters['description'] = (string) $description;
        }
        return ProcessRecurringResponse::initializeByString($this->call('card/processRecurring', $parameters));
    }

    /**
     * REST API call method
     * @param string $method API method name
     * @param array $parameters method call parameters
     * @return string raw API response
     * @throws ErrorException exception if there was an API error
     */
    private function call($method, $parameters) {
        $Request = new Request($method, $parameters);
        $Request->sign($this->publicKey, $this->privateKey);
        try {
            return $Request->execute();
        } catch (HttpClientErrorCodeException $Ex) {
            throw ErrorException::initializeByString($Ex->getMessage());
        }
    }
}
