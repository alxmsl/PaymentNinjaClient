<?php

namespace alxmsl\PaymentNinja;
use alxmsl\PaymentNinja\Response\AuthenticateResponse;
use alxmsl\PaymentNinja\Response\ProcessRecurringResponse;
use alxmsl\PaymentNinja\Response\ProcessResponse;
use alxmsl\PaymentNinja\Response\SuccessResponse;
use alxmsl\PaymentNinja\Response\TokenResponse;
use alxmsl\PaymentNinja\Response\UserResponse;

/**
 *
 * @author alxmsl
 */
final class Client {

    private $publicKey = '';

    private $secretKey = '';

    public function __construct($publicKey, $secretKey) {
        $this->publicKey = (string) $publicKey;
        $this->secretKey = (string) $secretKey;
    }

    public function userResolve($id, $email, $ip, $displayName = null, $phone = null) {
        $parameters = [
            'identifier' => (string) $id,
            'email'      => (string) $email,
            'ip'         => (string) $ip,
        ];
        if (!is_null($displayName)) {
            $parameters['display_name'] = (string) $displayName;
        }
        if (!is_null($phone)) {
            $parameters['phone'] = (string) $phone;
        }
        return UserResponse::initializeByString($this->call('user/resolve', $parameters));
    }

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

    public function userCancelRecurring($userId) {
        $parameters = [
            'user' => (string) $userId,
        ];
        return SuccessResponse::initializeByString($this->call('user/cancelRecurring', $parameters));
    }

    public function cardGetToken($number, $expirationMonth, $expirationYear, $securityCode, $callback = null) {
        $parameters = [
            'number' => (string) $number,
            'expiration_month' => (string) $expirationMonth,
            'expiration_year' => (string) $expirationYear,
            'security_code' => (string) $securityCode,
        ];
        if (!is_null($callback)) {
            $parameters['callback'] = (string) $callback;
        }
        return TokenResponse::initializeByString($this->call('card/getToken', $parameters));
    }

    public function cardProcess($userId, $orderId, $price, $currency, $description, $ip, $acsReturnUrl,
        $remember = null, $verifyCard = null, $recurring = null, $recurringInterval = null, $recurringTrial = null,
        array $attributes = []) {

        $parameters = [
            'user' => (string) $userId,
            'order_id' => (string) $orderId,
            'price' => (float) $price,
            'currency' => (string) $currency,
            'description' => (string) $description,
            'ip' => (string) $ip,
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

    public function cardAuthenticate($payerReponse, $merchantData) {
        $parameters = [
            'PaRes' => (string) $payerReponse,
            'MD' => (string) $merchantData,
        ];
        return AuthenticateResponse::initializeByString($this->call('card/authenticate', $parameters));
    }

    public function cardProcessRecurring($userId, $orderId = null) {
        $parameters = [
            'user' => (string) $userId,
        ];
        if (!is_null($orderId)) {
            $parameters['order_id'] = (string) $orderId;
        }
        return ProcessRecurringResponse::initializeByString($this->call('card/processRecurring', $parameters));
    }

    private function call($method, $parameters) {
        $Request = new Request($method, $parameters);
        $Request->sign($this->publicKey, $this->secretKey);
        return $Request->execute();
    }
}
