<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\IProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(IProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->listProduct();
        return view('products', compact('products'));
    }
    public function getProductDetail($slug)
    {
        // Tạo fake request với slug để tương thích với service hiện tại
        $request = new Request();
        $request->merge(['slug' => $slug]);

        $product = $this->productService->getProductWithVariants($request);
        return view('productDetail', compact('product'));
    }
}
