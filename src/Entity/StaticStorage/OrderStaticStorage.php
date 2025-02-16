<?php

namespace App\Entity\StaticStorage;


class  OrderStaticStorage
{
    public const ORDER_STATUS_CREATED = 0;
    public const ORDER_STATUS_PROCESSED = 1;
    public const ORDER_STATUS_COMPLETED = 2;
    public const ORDER_STATUS_DELIVERED = 3;
    public const ORDER_STATUS_CANCELED = 4;


    /**
     * @return string[]
     */
    public static function getOrderStatusList(): array
    {
        return [
            self::ORDER_STATUS_CREATED   => 'Created',
            self::ORDER_STATUS_PROCESSED => 'Processed',
            self::ORDER_STATUS_COMPLETED => 'Completed',
            self::ORDER_STATUS_DELIVERED => 'Delivered',
            self::ORDER_STATUS_CANCELED  => 'Canceled',
        ];
    }
}

