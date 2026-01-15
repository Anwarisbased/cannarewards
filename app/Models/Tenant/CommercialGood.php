<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class CommercialGood extends Model
{
    use TenantConnection;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'points_awarded',
        'msrp_cents',
        'strain_type',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_awarded' => 'integer',
        'msrp_cents' => 'integer',
    ];

    public function rewardCodes(): HasMany
    {
        return $this->hasMany(RewardCode::class, 'commercial_good_id');
    }
}
