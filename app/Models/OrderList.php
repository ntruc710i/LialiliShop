<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderList extends Model
{
    /**
     *  @OA\Schema(
     *     schema="OrderList",
     *     required={"id", "user_id", "product_id", "quantity", "total", "order_code"},
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
     *         property="quantity",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="total",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="order_code",
     *         type="string"
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
        'quantity',
        'total',
        'order_code'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
