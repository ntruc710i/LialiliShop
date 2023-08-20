<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     *  @OA\Schema(
     *     schema="Contact",
     *     required={"id", "name","email","phone","message"},
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         format="int32"
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="phone",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="message",
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
        'name',
        'email',
        'phone',
        'message',
        'read',
        'reply'
    ];
}
