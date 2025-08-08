<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
    protected $table = 'product_variant_value';

    protected $fillable = [
        'product_variant_id',
        'variant_option_id'
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function option()
    {
        return $this->belongsTo(VariantOption::class, 'variant_option_id');
    }
}
