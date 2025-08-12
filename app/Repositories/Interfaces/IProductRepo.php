<?php

namespace App\Repositories\Interfaces;

interface IProductRepo extends IBaseRepo
{
    public function listProduct();
    public function getProductWithVariants($slug);
    public function getProductWithVariantsById($id);
    public function getProductsByCategory($categoryId);
    public function getProductWithVariantsAndGroupsById($id);

    // Variant-related methods
    public function deleteVariantValuesByOptionIds($optionIds);
    public function deleteProductVariantByOptionIds($optionIds);
    public function deleteVariantValuesByGroupIds($groupIds);
    public function createVariant($data);
    public function createVariantValue($data);
    public function variantExistsWithOptions($productId, $optionIds);
    public function getProductVariantsById($productId);
    public function getProductVariantsWithOptions($productId);
    public function deleteVariantValuesByVariantIds($variantIds);
    public function deleteVariantsByIds($variantIds);
}
