<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class TestDTO extends Data
{
    public function __construct(
        public string $name,
        public int $age,
        public bool $isActive,
    ) {}
}
