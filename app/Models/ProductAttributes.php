<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributes extends Model
{
    Protected $fillable = [
        "product_id",
        "size",
        "color",
        "stock",
    ];

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}
