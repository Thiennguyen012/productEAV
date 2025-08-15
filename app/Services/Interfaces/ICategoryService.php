<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface ICategoryService
{
    public function getCategoriesList();
    public function newCategory(Request $request);
    public function updateCategory($categoryId, Request $request);
    public function deleteCategory($categoryId);
}
