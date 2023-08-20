<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributes extends Model
{

    /**
     *  @OA\Schema(
     *     schema="ProductAttributes",
     *     required={"id", "product_id", "size", "color", "stock"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OA\Property(
     *         property="product_id",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="size",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="color",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="stock",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time"
     *     ),
     *     @OA\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time"
     *     )
     * ),
     */
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
