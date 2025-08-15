<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICategoryService;
use App\Services\Interfaces\IProductService;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    public function __construct(IProductService $productService, ICategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->getCategoriesList();
        $products = $this->productService->listProduct($request);
        return view('Admin.products', compact('products', 'categories'));
    }
    public function newProduct(Request $request)
    {
        $this->productService->newProduct($request);
        return redirect()->back()->with('success', 'Tạo mới sản phẩm thành công!');
    }
    public function edit($id)
    {
        $product = $this->productService->getProductForEdit($id);
        $categories = $this->categoryService->getCategoriesList();

        return view('Admin.updateProduct', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        try {
            $this->productService->updateProduct($request, $id);
            return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
