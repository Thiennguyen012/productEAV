<?php

namespace App\Services;

use App\Repositories\Interfaces\ICategoryRepo;
use App\Services\Interfaces\ICategoryService;

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
}
