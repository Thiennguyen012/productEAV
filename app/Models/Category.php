<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'category_name',
        'slug',
        'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
