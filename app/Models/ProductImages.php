<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImages extends Model
{
    /**
     *  @OA\Schema(
     *     schema="ProductImages",
     *     required={"id", "product_id", "image"},
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
     *         property="image",
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
    Protected $fillable = [
        "product_id",
        "image",
    ];
}
