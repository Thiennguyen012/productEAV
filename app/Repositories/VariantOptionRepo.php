<?php

namespace App\Repositories;

use App\Models\Product\VariantOption;
use App\Repositories\Interfaces\IVariantOptionRepo;

class VariantOptionRepo extends BaseRepo implements IVariantOptionRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(VariantOption $variantOption)
    {
        parent::__construct($variantOption);
    }
}
