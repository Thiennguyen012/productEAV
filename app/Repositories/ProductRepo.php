<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IProductRepo;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;

class ProductRepo extends BaseRepo implements IProductRepo
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function listProduct()
    {
        return $this->model->with(['category', 'variants.options', 'variantGroups.options'])->paginate(12);
    }

    public function getProductWithVariants($slug)
    {
        return $this->model->with([
            'category',
            'variants' => function ($query) {
                $query->where('is_active', 'true');
            },
            'variants.options',
            'variantGroups.options'
        ])->where('slug', $slug)->first();
    }
    public function getProductWithVariantsById($id)
    {
        return $this->model->with([
            'category',
            'variants',
            'variants.options',
            'variantGroup.options'
        ])->find($id);
    }
    public function getProductsByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->where('is_active', 'true')
            ->with(['category', 'variants'])
            ->get();
    }
    public function getProductWithVariantsAndGroupsById($id)
    {
        return $this->model->with([
            'category',
            'variantGroups.options',
            'variants.values.option.group'
        ])->findOrFail($id);
    }

    /**
     * Delete variant values by option IDs
     */
    public function deleteVariantValuesByOptionIds($optionIds)
    {
        return DB::table('product_variant_value')
            ->whereIn('variant_option_id', $optionIds)
            ->delete();
    }

    /**
     * Delete variant values by group IDs
     */
    public function deleteVariantValuesByGroupIds($groupIds)
    {
        $optionIds = DB::table('variant_option')
            ->whereIn('variant_group_id', $groupIds)
            ->pluck('id')
            ->toArray();

        if (!empty($optionIds)) {
            return $this->deleteVariantValuesByOptionIds($optionIds);
        }

        return 0;
    }

    /**
     * Create product variant
     */
    public function createVariant($data)
    {
        return DB::table('product_variant')->insertGetId($data);
    }

    /**
     * Create variant value
     */
    public function createVariantValue($data)
    {
        return DB::table('product_variant_value')->insert($data);
    }

    /**
     * Check if variant exists with given option combination
     */
    public function variantExistsWithOptions($productId, $optionIds)
    {
        $existingVariants = DB::table('product_variant')
            ->join('product_variant_value', 'product_variant.id', '=', 'product_variant_value.product_variant_id')
            ->where('product_variant.product_id', $productId)
            ->select('product_variant.id')
            ->groupBy('product_variant.id')
            ->havingRaw('COUNT(DISTINCT product_variant_value.variant_option_id) = ?', [count($optionIds)])
            ->get();

        foreach ($existingVariants as $variant) {
            $variantOptionIds = DB::table('product_variant_value')
                ->where('product_variant_id', $variant->id)
                ->pluck('variant_option_id')
                ->toArray();

            sort($variantOptionIds);
            sort($optionIds);

            if ($variantOptionIds === $optionIds) {
                return true;
            }
        }

        return false;
    }
}
