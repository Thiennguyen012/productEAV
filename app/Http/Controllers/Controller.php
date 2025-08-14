<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    //
        public function getCurrSession(Request $request)
    {
        // Tạo hoặc lấy session cart ID
        if (!$request->session()->has('cart_session_id')) {
            // Nếu chưa có session cart, tạo mới
            $session_id = 'cart_' . uniqid() . '_' . time();
            $request->session()->put('cart_session_id', $session_id);
        } else {
            // Lấy session cart ID đã có
            $session_id = $request->session()->get('cart_session_id');
        }
        return $session_id;
    }
}
