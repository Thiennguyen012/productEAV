<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface IProductService
{
    public function listProduct();
    public function getProductWithVariants(Request $request);
    public function newProduct(Request $request);
    public function getProductForEdit($id);
    public function updateProduct(Request $request,$id);
}
