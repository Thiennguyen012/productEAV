<?php

namespace App\Repositories;

use App\Models\Product\VariantGroup;
use App\Repositories\Interfaces\IVariantGroupRepo;

class VariantGroupRepo extends BaseRepo implements IVariantGroupRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(VariantGroup $variantGroup)
    {
        parent::__construct($variantGroup);
    }
}
