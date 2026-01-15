<?php

namespace App\Data\Tenant;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CommercialGoodData extends Data
{
    public function __construct(
        public int $id,
        public string $sku,
        public string $name,
        public ?string $description,
        public int $points_awarded,
        public string $image_url,
        public bool $is_active,
    ) {}
}
