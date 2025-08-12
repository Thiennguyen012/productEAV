<?php

namespace App\Repositories;

use App\Models\Cart\Cart;
use App\Repositories\Interfaces\ICartRepo;
use Illuminate\Http\Request;

class CartRepo extends BaseRepo implements ICartRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(Cart $cart)
    {
        parent::__construct($cart);
    }
}
