<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class VariantGroup extends Model
{
    protected $table = 'variant_group';

    protected $fillable = [
        'name',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(VariantOption::class);
    }
}
