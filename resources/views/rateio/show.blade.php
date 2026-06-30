<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Rateio — {{ $space->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">Saldos</div>
                        <div class="table-responsive">
                                                <table class="table mb-0">
                            <thead>
                                <tr><th>Participante</th><th class="text-end">Saldo</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($balances as $balance)
                                    <tr>
                                        <td>{{ $balance['name'] }}</td>
                                        <td class="text-end {{ $balance['balance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            R$ {{ number_format($balance['balance'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted text-center py-4">Sem dados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">Transações sugeridas</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($transactions as $transaction)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $transaction['from_name'] }} &rarr; {{ $transaction['to_name'] }}</span>
                                    <span class="fw-bold">R$ {{ number_format($transaction['amount'], 2, ',', '.') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nenhuma pendência. Tudo quitado!</li>
                            @endforelse
                        </ul>

                        @if (count($transactions))
                            <div class="card-body">
                                <h6>Registrar pagamento</h6>
                                <form action="{{ route('spaces.settlements.store', $space) }}" method="POST" class="row g-2">
                                    @csrf
                                    <div class="col-md-3">
                                        <select name="payer_id" class="form-select" required>
                                            <option value="">De</option>
                                            @foreach ($balances as $balance)
                                                <option value="{{ $balance['user_id'] }}">{{ $balance['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="receiver_id" class="form-select" required>
                                            <option value="">Para</option>
                                            @foreach ($balances as $balance)
                                                <option value="{{ $balance['user_id'] }}">{{ $balance['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="Valor" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="method" class="form-select">
                                            <option value="pix">Pix</option>
                                            <option value="dinheiro">Dinheiro</option>
                                            <option value="cartao">Cartão</option>
                                            <option value="transferencia">Transferência</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary w-100">Registrar</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
