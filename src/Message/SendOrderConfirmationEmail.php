<?php

namespace App\Message;

class SendOrderConfirmationEmail
{
    public function __construct(private readonly int $orderId)
    {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}