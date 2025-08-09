<?php

namespace App\Repositories\Interfaces;

interface IVariantGroupRepo extends IBaseRepo
{
    public function getGroupIdsByProductId($productId);
    public function deleteByIds($ids);
    public function getGroupsWithOptionsByProductId($productId);
}
