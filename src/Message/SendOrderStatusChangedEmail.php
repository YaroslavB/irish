<?php

namespace App\Message;

class SendOrderStatusChangedEmail
{
    public function __construct(
        private readonly int $orderId,
        private readonly int $newStatus,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getNewStatus(): int
    {
        return $this->newStatus;
    }
}