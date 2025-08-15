<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICategoryService;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    protected $categoryService;
    public function __construct(ICategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function showAll(Request $request)
    {
        $categories = $this->categoryService->getCategoriesList();

        // Apply simple filtering if requested
        if ($request->has('category_name') && $request->category_name) {
            $categories = $categories->filter(function ($category) use ($request) {
                return stripos($category->category_name, $request->category_name) !== false;
            });
        }

        if ($request->has('status') && $request->status !== '') {
            $categories = $categories->filter(function ($category) use ($request) {
                return $category->is_active === $request->status;
            });
        }

        return view('Admin.categories', compact('categories'));
    }

    public function updateCategory($categoeyId, Request $request)
    {
        $result = $this->categoryService->updateCategory($categoeyId, $request);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật category thành công!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật category thất bại!'
            ], 400);
        }
    }

    public function deleteCategory($categoeyId)
    {
        $result = $this->categoryService->deleteCategory($categoeyId);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa category thành công!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Xóa category thất bại!'
            ]);
        }
    }
}
