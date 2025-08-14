<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ICartService
{
    public function addToCart($session_id, Request $request);
    public function showCart($session_id);
    public function getCartCount($session_id);
}
