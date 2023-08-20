<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderList;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     *  @OA\Schema(
     *     schema="Order",
     *     required={"id", "user_id", "order_code", "phone", "address", "total_price", "status"},
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
     *         property="order_code",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="phone",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="address",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="total_price",
     *         type="integer"
     *     ),
     *     @OA\Property(
     *         property="status",
     *         type="int"
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
        'order_code',
        'phone',
        'address',
        'total_price',
        'status'
    ];

    // Table relation with order lists
    public function order_list(): HasMany
    {
        return $this->hasMany(OrderList::class, 'order_code', 'order_code');
    }

}
