<?php

namespace App\Repositories;

use App\Models\Order\OrderItem;
use App\Repositories\Interfaces\IOrderItemRepo;

class OrderItemRepo extends BaseRepo implements IOrderItemRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(OrderItem $orderItem)
    {
        parent::__construct($orderItem);
    }
}
