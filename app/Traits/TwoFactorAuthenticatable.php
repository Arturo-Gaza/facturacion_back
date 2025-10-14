<?php

namespace App\Traits;

use App\Notifications\TwoFactorCodeNotification;
use PragmaRX\Google2FA\Google2FA;

trait TwoFactorAuthenticatable
{
    /**
     * Generar clave secreta para Google Authenticator
     */
    public function generate2FASecret()
    {
        $google2fa = new Google2FA();
        $this->google2fa_secret = $google2fa->generateSecretKey();
        $this->save();

        return $this->google2fa_secret;
    }

    /**
     * Generar código 2FA temporal (para correo electrónico)
     */
    public function generateTwoFactorCode()
    {
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();

    }

    /**
     * Resetear código 2FA
     */
    public function resetTwoFactorCode()
    {
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    /**
     * Verificar código 2FA manualmente
     */
    public function verifyTwoFactorCode($code)
    {
        return $this->two_factor_code === $code &&
            $this->two_factor_expires_at &&
            now()->lt($this->two_factor_expires_at);
    }
}
