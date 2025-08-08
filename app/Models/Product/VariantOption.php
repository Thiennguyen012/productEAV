<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class VariantOption extends Model
{
    protected $table = 'variant_option';

    protected $fillable = [
        'variant_group_id',
        'value'
    ];

    public function group()
    {
        return $this->belongsTo(VariantGroup::class, 'variant_group_id');
    }

    public function productVariantValues()
    {
        return $this->hasMany(ProductVariantValue::class);
    }
}
