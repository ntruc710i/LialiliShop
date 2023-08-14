<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributes extends Model
{
    Protected $fillable = [
        "product_id",
        "size",
        "color",
        "stock",
    ];
}
