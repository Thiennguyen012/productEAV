<?php

namespace App\Repositories;

use App\Models\Cart\CartItem;
use App\Repositories\Interfaces\ICartItemRepo;

class CartItemRepo extends BaseRepo implements ICartItemRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(CartItem $cartItem)
    {
        parent::__construct($cartItem);
    }
}
