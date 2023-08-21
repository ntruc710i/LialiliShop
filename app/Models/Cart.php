<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
   /**
     *  @OA\Schema(
     *     schema="Cart",
     *     required={"id", "user_id", "product_id", "product_attribute_id", "quantity"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="product_id",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="product_attribute_id",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="quantity",
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
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'product_attribute_id',
        'quantity'
    ];

    // Table relation with product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productAttributes(): BelongsTo
    {
        return $this->belongsTo(ProductAttributes::class, 'product_attribute_id');
    }

}
