<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variant';

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'quantity',
        'is_active',
        'image'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class);
    }

    // shortcut: lấy trực tiếp các option
    public function options()
    {
        return $this->belongsToMany(VariantOption::class, 'product_variant_value');
    }
}
