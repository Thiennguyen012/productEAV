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

    public function index(){
        $products = $this->productService->listProduct();
        return view('Admin.products',compact('products'));
    }
    public function newProduct(Request $request){
        $this->productService->newProduct($request);
        return redirect()->back()->with('success','Tạo mới sản phẩm thành công!');
    }

}
