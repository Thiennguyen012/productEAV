<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ICategoryService;
use App\Services\Interfaces\IProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService, $categoryService;
    public function __construct(IProductService $productService, ICategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->listProduct($request);
        $category = $this->categoryService->getCategoriesList();
        return view('products', compact('products','category'));
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
