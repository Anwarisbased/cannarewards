<?php

namespace App\Http\Controllers\Scan;

use App\Data\Tenant\CommercialGoodData;
use App\Models\Tenant\RewardCode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeaserController
{
    public function __invoke(Request $request, string $code): Response
    {
        // Find RewardCode by code
        $rewardCode = RewardCode::where('code', $code)->first();

        if (! $rewardCode) {
            // If code doesn't exist, return invalid error
            return Inertia::render('Scan/Error', ['reason' => 'invalid']);
        }

        // Check Status
        if ($rewardCode->status === 'used') {
            // If used -> Return Inertia::render('Scan/Error', ['reason' => 'used']).
            return Inertia::render('Scan/Error', ['reason' => 'used']);
        }

        if ($rewardCode->status === 'void') {
            // If void -> Return Inertia::render('Scan/Error', ['reason' => 'invalid']).
            return Inertia::render('Scan/Error', ['reason' => 'invalid']);
        }

        if ($rewardCode->status === 'active') {
            // If active -> Return Inertia::render('Scan/Teaser', ['product' => CommercialGoodData::from($code->commercialGood)]).
            return Inertia::render('Scan/Teaser', [
                'product' => CommercialGoodData::from($rewardCode->commercialGood),
            ]);
        }

        // Default case for any other statuses
        return Inertia::render('Scan/Error', ['reason' => 'invalid']);
    }
}
