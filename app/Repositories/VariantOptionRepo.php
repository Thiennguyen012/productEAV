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

    public function getOptionIdsByGroupId($groupId)
    {
        return $this->model->where('variant_group_id', $groupId)->pluck('id')->toArray();
    }

    public function deleteByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function deleteByGroupIds($groupIds)
    {
        return $this->model->whereIn('variant_group_id', $groupIds)->delete();
    }
}
