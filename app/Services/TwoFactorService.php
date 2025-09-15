<?php
namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecretKey()
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQRCodeUrl($companyName, $companyEmail, $secret)
    {
        return $this->google2fa->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    public function generateQRCodeSvg($qrCodeUrl)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        return $writer->writeString($qrCodeUrl);
    }

    public function verifyCode($secret, $code)
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
}