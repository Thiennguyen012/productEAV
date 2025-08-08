<?php

namespace App\Repositories\Interfaces;

interface IProductRepo extends IBaseRepo
{
    public function listProduct();
    public function getProductWithVariants($id);
    public function getProductsByCategory($categoryId);
}
