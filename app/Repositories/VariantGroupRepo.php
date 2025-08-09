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

    public function getGroupIdsByProductId($productId)
    {
        return $this->model->where('product_id', $productId)->pluck('id')->toArray();
    }

    public function deleteByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function getGroupsWithOptionsByProductId($productId)
    {
        return $this->model->with('options')
            ->where('product_id', $productId)
            ->get();
    }
}
