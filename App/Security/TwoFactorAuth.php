<?php

namespace App\Security;

use PragmaRX\Google2FA\Google2FA;

use \App\Config;

class TwoFactorAuth
{
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }
    /**
     * Generate a secret key for 2FA
     * @return string - The generated secret key
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }
     public function getQrUrl(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            Config::CompanyName,
            $email,
            $secret
        );
    }


    /**
     * Verify the 6-digit code provided by the user
     */
    public function verifyCode(string $secret, string $user_code): bool
    {
        return $this->google2fa->verifyKey($secret, $user_code);
    }
}
