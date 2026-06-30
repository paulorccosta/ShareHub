<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function store(Request $request, Space $space)
    {
        $validated = $request->validate([
            'payer_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:payer_id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'nullable|in:pix,dinheiro,cartao,transferencia',
            'status' => 'nullable|in:pendente,pago',
        ]);

        $validated['status'] = $validated['status'] ?? 'pago';
        $validated['paid_at'] = $validated['status'] === 'pago' ? now() : null;

        $space->settlements()->create($validated);

        return redirect()->route('rateio.show', $space)->with('status', 'Pagamento registrado.');
    }
}
