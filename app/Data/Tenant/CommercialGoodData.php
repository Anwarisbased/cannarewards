<?php

namespace App\Data\Tenant;

use Spatie\LaravelData\Data;

class CommercialGoodData extends Data
{
    public function __construct(
        public int $id,
        public string $sku,
        public string $name,
        public int $points_awarded,
        public int $msrp_cents,
        public string $strain_type,
        public ?string $image_url,
        public bool $is_active,
    ) {}
}
