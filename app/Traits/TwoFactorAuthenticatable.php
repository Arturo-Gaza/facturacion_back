<?php
namespace App\Traits;

use App\Notifications\TwoFactorCodeNotification;
use PragmaRX\Google2FA\Google2FA;

trait TwoFactorAuthenticatable
{
    public function generateTwoFactorCode()
    {
        // Generar código de 6 dígitos
        $this->timestamps = false;
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function enableTwoFactor()
    {
        $this->two_factor_enabled = true;
        $this->save();
    }

    public function disableTwoFactor()
    {
        $this->two_factor_enabled = false;
        $this->two_factor_secret = null;
        //$this->two_factor_recovery_codes = null;
        $this->save();
    }
}