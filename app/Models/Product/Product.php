<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'product_name',
        'slug',
        'description',
        'image',
        'is_active',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function variantGroups()
    {
        return $this->hasMany(VariantGroup::class);
    }
}
