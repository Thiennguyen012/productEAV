<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICategoryService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $categoryService;
    public function __construct(ICategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function index(){
        return view('Admin.home');
    }
    public function showNewProduct(){
        $categories = $this->categoryService->getCategoriesList();
        return view('Admin.newProduct',compact('categories'));
    }
}
