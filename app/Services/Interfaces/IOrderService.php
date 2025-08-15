<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IOrderService
{
    public function getAllOrderWithItems(Request $request);

    public function newOrder($session_id, Request $request);
}
