<?php

namespace App\Recaptcha;

use ReCaptcha\ReCaptcha;

class RecaptchaValidator
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function verify(?string $gRecaptchaResponse, ?string $remoteIp = null): bool
    {
        if (empty($gRecaptchaResponse)) {
            return false;
        }

        $recaptcha = new ReCaptcha($this->secretKey);
        $response = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

        return $response->isSuccess();
    }
}