<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class RewardCode extends Model
{
    use TenantConnection;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'commercial_good_id',
        'batch_id',
        'status',
        'user_id',
        'claimed_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
    ];

    public function commercialGood(): BelongsTo
    {
        return $this->belongsTo(CommercialGood::class, 'commercial_good_id');
    }
}
