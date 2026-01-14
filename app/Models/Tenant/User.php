<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'uuid',
        'phone',
        'email',
        'name',
        'dob',
        'points_balance',
        'lifetime_points',
        'current_rank_key',
        'referral_code',
        'referred_by_id',
        'signals',
        'marketing_opt_in',
    ];

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
        'dob' => 'date',
        'points_balance' => 'integer',
        'lifetime_points' => 'integer',
        'marketing_opt_in' => 'boolean',
        'signals' => 'array',
        'uuid' => 'string',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'points_balance' => 0,
        'lifetime_points' => 0,
        'current_rank_key' => 'member',
        'marketing_opt_in' => false,
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
