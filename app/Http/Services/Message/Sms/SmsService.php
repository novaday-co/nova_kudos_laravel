<?php

namespace App\Http\Services\Message\Sms;

use App\Http\Interfaces\MessageInterface;
use Kavenegar\KavenegarApi;

class SmsService implements MessageInterface
{
    private string $sender = '1000596446';
    private string $receptor;
    private string $message;

    public function fire()
    {
        $kavenegarSystem = new KavenegarApi('516F5552376F74586A30642F49734739546770324B554632513146726B466A6D443732624D6179304A474D3D');
        $kavenegarSystem->Send($this->sender, $this->receptor, $this->message);
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

