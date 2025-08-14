<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IOrderService
{
    public function newOrder($session_id, Request $request);
}
