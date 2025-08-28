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

    public function listProduct($productName = null, $category_id = null, $status = null, $sort = null, $direction = null)
    {
        $query =  $this->model->with(['category', 'variants.options', 'variantGroups.options']);
        if ($productName) {
            // Sử dụng LIKE với SQLite (case-insensitive search)
            $query = $query->where('product_name', 'LIKE', "%{$productName}%");
        }
        if ($category_id) {
            $query = $query->where('category_id', $category_id);
        }
        if ($status) {
            $query = $query->where('is_active', $status);
        }
        if ($sort) {
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
            switch ($sort) {
                case 'product_name':
                case 'category_id':
                    $query = $query->orderBy($sort, $direction);
                default:
                    $query = $query->orderBy('id', $direction);
            }
        } else {
            $query = $query->orderBy('id', 'desc');
        }
        return $query->paginate(12);
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
     * Delete product variants by option IDs
     * Xóa các product variants có chứa bất kỳ option nào trong danh sách optionIds
     */
    public function deleteProductVariantByOptionIds($optionIds)
    {
        // Lấy danh sách variant IDs có chứa các options này
        $variantIds = DB::table('product_variant_value')
            ->whereIn('variant_option_id', $optionIds)
            ->pluck('product_variant_id')
            ->unique()
            ->toArray();

        if (!empty($variantIds)) {
            // Xóa tất cả variant values của các variants này
            DB::table('product_variant_value')
                ->whereIn('product_variant_id', $variantIds)
                ->delete();

            // Xóa các variants trong bảng product_variant
            return DB::table('product_variant')
                ->whereIn('id', $variantIds)
                ->delete();
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

    /**
     * Get all product variants by product ID (simple version)
     */
    public function getProductVariantsById($productId)
    {
        return DB::table('product_variant')
            ->where('product_id', $productId)
            ->select('id', 'sku', 'price', 'quantity')
            ->get()
            ->toArray();
    }

    /**
     * Get product variants with their options (detailed version)
     */
    public function getProductVariantsWithOptions($productId)
    {
        return DB::table('product_variant')
            ->join('product_variant_value', 'product_variant.id', '=', 'product_variant_value.product_variant_id')
            ->join('variant_option', 'product_variant_value.variant_option_id', '=', 'variant_option.id')
            ->join('variant_group', 'variant_option.variant_group_id', '=', 'variant_group.id')
            ->where('product_variant.product_id', $productId)
            ->select(
                'product_variant.id',
                'product_variant.price',
                'product_variant.quantity',
                'product_variant.sku',
                'variant_option.id as option_id',
                'variant_option.value as option_value',
                'variant_group.id as group_id',
                'variant_group.name as group_name'
            )
            ->get()
            ->groupBy('id')
            ->map(function ($variantData, $variantId) {
                $firstItem = $variantData->first();
                return [
                    'id' => $variantId,
                    'price' => $firstItem->price,
                    'quantity' => $firstItem->quantity,
                    'sku' => $firstItem->sku,
                    'options' => $variantData->map(function ($item) {
                        return (object) [
                            'id' => $item->option_id,
                            'value' => $item->option_value,
                            'variant_group_id' => $item->group_id,
                            'group_name' => $item->group_name
                        ];
                    })->toArray()
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Delete variant values by variant IDs (multiple variants)
     */
    public function deleteVariantValuesByVariantIds($variantIds)
    {
        return DB::table('product_variant_value')
            ->whereIn('product_variant_id', $variantIds)
            ->delete();
    }

    /**
     * Delete variants by IDs
     */
    public function deleteVariantsByIds($variantIds)
    {
        return DB::table('product_variant')
            ->whereIn('id', $variantIds)
            ->delete();
    }
}
