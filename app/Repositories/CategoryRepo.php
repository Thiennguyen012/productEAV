<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\ICategoryRepo;

class CategoryRepo extends BaseRepo implements ICategoryRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }
}
