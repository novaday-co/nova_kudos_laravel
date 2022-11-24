<?php

namespace App\Http\Services\Message\Sms;

use App\Http\Interfaces\MessageInterface;
use Kavenegar\KavenegarApi;

class SmsService implements MessageInterface
{
    private string $receptor;
    private string $message;

    public function fire()
    {
        $kavenegarSystem = new KavenegarApi(env('KAVENEGAR_API_KEY'));
        $kavenegarSystem->Send(env('KAVENEGAR_SENDER_SMS'), $this->receptor, $this->message);
    }

    public function getReceptor(): string
    {
        return $this->receptor;
    }

    public function setReceptor(string $receptor)
    {
        $this->receptor = $receptor;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }
}

