<?php

namespace App\Repositories\Interfaces;

interface IVariantOptionRepo extends IBaseRepo
{
    public function getOptionIdsByGroupId($groupId);
    public function deleteByIds($ids);
    public function deleteByGroupIds($groupIds);
}
