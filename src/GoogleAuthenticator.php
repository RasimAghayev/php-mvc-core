<?php

declare(strict_types=1);

namespace RasimAghayev\PhpMvcCore;

use OTPhp\OTP\TOTP;

class GoogleAuthenticator
{
    private TOTP $otp;

    public function __construct(string $secret = '')
    {
        if (empty($secret)) {
            $this->otp = TOTP::create(
                rtrim(base64_encode(random_bytes(32)), '='),
                6,
                'sha1',
                30
            );
        } else {
            $this->otp = TOTP::create(
                $secret,
                6,
                'sha1',
                30
            );
        }
    }

    public function getSecret(): string
    {
        return $this->otp->getSecret();
    }

    public function getQrUrl(string $account, string $issuer = 'php-mvc-core'): string
    {
        $otpUrl = $this->otp->getQrCodeURL($issuer, $account);
        return 'https://api.qrserver.com/1/v1/api/qr/' . http_build_query([
            'data' => $otpUrl,
            'size' => '200x200',
        ]);
    }

    public function checkSecret(string $code): bool
    {
        return hash_equals($this->otp->getSecret(), $this->otp->getSecret()) &&
               $this->otp->check($code);
    }

    public function getCode(): string
    {
        return $this->otp->getSecret();
    }
}
