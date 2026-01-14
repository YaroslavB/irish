<?php

namespace App\Form\Handler;

use App\Entity\Order;
use App\Utils\Manager\OrderManager;

class OrderFormHandler
{

    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @param OrderManager $orderManager
     */
    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public function processEditForm(Order $order)
    {
        $this->orderManager->save($order);

        return $order;
    }

}
