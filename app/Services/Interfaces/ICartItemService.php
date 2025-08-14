<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ICartItemService
{
    public function updateItem($itemId, Request $request);
    public function delteItem($itemId);
}
