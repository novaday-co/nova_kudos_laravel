<?php

namespace App\Services\Message;

use App\Http\Interfaces\MessageInterface;

class MessageService
{
    public function __construct(private MessageInterface $message)
    {
    }

    public function send()
    {
        return $this->message->fire();
    }
}
