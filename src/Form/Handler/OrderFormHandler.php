<?php

namespace App\Form\Handler;

use App\Entity\Order;
use App\Message\SendOrderStatusChangedEmail;
use App\Utils\Manager\OrderManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderFormHandler
{
    private OrderManager $orderManager;
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;

    public function __construct(
        OrderManager $orderManager,
        EntityManagerInterface $entityManager,
        MessageBusInterface $bus
    ) {
        $this->orderManager = $orderManager;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    public function processEditForm(Order $order): Order
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($order);
        $oldStatus = $originalData['status'] ?? null;
        $newStatus = $order->getStatus();

        $this->orderManager->save($order);

        if ($oldStatus !== null && $oldStatus !== $newStatus) {
            $this->bus->dispatch(new SendOrderStatusChangedEmail($order->getId(), $newStatus));
        }

        return $order;
    }
}
