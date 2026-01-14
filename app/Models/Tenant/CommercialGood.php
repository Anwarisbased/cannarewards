<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class CommercialGood extends Model
{
    use HasFactory, TenantConnection;

    protected $fillable = [
        'sku',
        'name',
        'points_awarded',
        'msrp_cents',
        'strain_type',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'points_awarded' => 'integer',
        'msrp_cents' => 'integer',
        'is_active' => 'boolean',
    ];
}
