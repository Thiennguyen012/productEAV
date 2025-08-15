<?php

namespace App\Repositories\Interfaces;

interface IOrderRepo extends IBaseRepo
{
    /**
     * Get cart by session ID with all related data
     */
    public function getAllOrderWithItems($customer_name = null, $status = null, $sort = null, $direction = null);

    public function getCartBySession($session_id);

    /**
     * Get cart items with product variant details using eager loading
     */
    public function getCartItemsWithVariants($session_id);
}
