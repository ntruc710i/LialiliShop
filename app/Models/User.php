<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

    /**
     *  @OA\Schema(
     *     schema="User",
     *     required={"id", "name", "email", "role", "password"},
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
     *         property="address",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="role",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="phone",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="avatar",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="provider",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="provider_id",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="provider_token",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="email_verified_at",
     *         type="string",
     *         format="date-time"
     *     ),
     *     @OA\Property(
     *         property="password",
     *         type="string"
     *     ),
     *     @OA\Property(
     *         property="remember_token",
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
class User extends Authenticatable
{
    
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'role',
        'phone',
        'avatar',
        'provider',
        'provider_id',
        'provider_token',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
