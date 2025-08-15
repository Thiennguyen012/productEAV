<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICartService;
use App\Services\Interfaces\IOrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderSevice, $cartService;
    public function __construct(IOrderService $orderSevice, ICartService $cartService)
    {
        $this->orderSevice = $orderSevice;
        $this->cartService = $cartService;
    }

    public function getAll(Request $request){
        $orderList = $this->orderSevice->getAllOrderWithItems($request);
        return view('Admin.order', compact('orderList'));
    }
}
