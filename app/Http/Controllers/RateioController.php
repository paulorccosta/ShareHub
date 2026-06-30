<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Services\RateioService;

class RateioController extends Controller
{
    public function show(Space $space, RateioService $rateioService)
    {
        $result = $rateioService->calcular($space);

        return view('rateio.show', [
            'space' => $space,
            'balances' => $result['balances'],
            'transactions' => $result['transactions'],
        ]);
    }
}
