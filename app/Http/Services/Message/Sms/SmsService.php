<?php

namespace App\Http\Services\Message\Sms;

use App\Http\Interfaces\MessageInterface;
use Kavenegar\KavenegarApi;

class SmsService implements MessageInterface
{
    private string $receptor;
    private string $otp_code;
    private string $template = 'verify';

    public function fire()
    {
        try {
            $kavenegar = new KavenegarApi(env('KAVENEGAR_API_KEY'));
            $kavenegar->VerifyLookup($this->receptor, $this->otp_code, null, null, $this->template);
        } catch (\Exception $exception)
        {

        }
    }

    public function getReceptor(): string
    {
        return $this->receptor;
    }

    public function setReceptor(string $receptor)
    {
        $this->receptor = $receptor;
    }

    public function getOtpCode(): string
    {
        return $this->otp_code;
    }

    public function setOtpCode(string $otp_code)
    {
        $this->otp_code = $otp_code;
    }
}

