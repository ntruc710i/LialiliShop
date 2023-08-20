<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @OA\Schema(
     *     schema="Product",
     *     required={"id", "title", "category_id", "slug", "prince", "description", "image"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OA\Property(
     *         property="title",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="category_id",
     *         type="integer",
     *         format="int32"
     *     ),
     *     
     *     @OA\Property(
     *         property="slug",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="price",
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string"
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
    use HasFactory;

    Protected $fillable = [
        "title",
        "category_id",
        "slug",
        "price",
        "count",
        "description",
        "image",
        "rate",
        
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttributes::class, 'product_id');
    }
    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImages::class, 'product_id');
    }
    
}
