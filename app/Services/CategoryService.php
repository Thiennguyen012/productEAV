<?php

namespace App\Services;

use App\Repositories\Interfaces\ICategoryRepo;
use App\Services\Interfaces\ICategoryService;
use Illuminate\Http\Request;

class CategoryService implements ICategoryService
{
    protected $categoryRepo;
    public function __construct(ICategoryRepo $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }
    public function getCategoriesList(){
        return $this->categoryRepo->all();
    }
    public function newCategory(Request $request)
    {
        return $this->categoryRepo->create([
            'category_name' => $request->input('category_name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
        ]);
    }

    public function updateCategory($categoryId,Request $request)
    {
        $category = $this->categoryRepo->find($categoryId)->update([
            'category_name' => $request->input('category_name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
        ]);
        return $category;
    }
    public function deleteCategory($categoryId)
    {
        return $this->categoryRepo->find($categoryId)->delete();
    }
}
