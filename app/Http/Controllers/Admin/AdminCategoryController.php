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

        // Apply sorting if requested
        if ($request->has('sort') && $request->sort) {
            $sortField = $request->sort;
            $categories = $categories->sortBy($sortField);
        }

        return view('Admin.categories', compact('categories'));
    }

    public function getCategoryById($categoeyId)
    {
        $category = $this->categoryService->getCategoryById($categoeyId);
        return view('Admin.updateCategory', compact('category'));
    }

    public function showNewCategory()
    {
        return view('Admin.newCategory');
    }

    public function newCategory(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'category_name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:categories,slug',
                'description' => 'nullable|string'
            ]);

            // Create new category
            $result = $this->categoryService->newCategory($request);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tạo danh mục mới thành công!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tạo danh mục thất bại!'
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
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
