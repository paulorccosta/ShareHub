<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-primary h-100">
                        <div class="card-body">
                            <div class="small">Espaços</div>
                            <div class="fs-2 fw-bold">{{ $totalSpaces }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-success h-100">
                        <div class="card-body">
                            <div class="small">Eventos abertos</div>
                            <div class="fs-2 fw-bold">{{ $openEventsCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-warning h-100">
                        <div class="card-body">
                            <div class="small">Gasto no mês</div>
                            <div class="fs-2 fw-bold">R$ {{ number_format($monthTotal, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card {{ $overallBalance >= 0 ? 'text-bg-info' : 'text-bg-danger' }} h-100">
                        <div class="card-body">
                            <div class="small">Seu saldo geral</div>
                            <div class="fs-2 fw-bold">R$ {{ number_format($overallBalance, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Eventos abertos</span>
                            <a href="{{ route('spaces.create') }}" class="btn btn-sm btn-primary">Novo espaço</a>
                        </div>
                        <ul class="list-group list-group-flush">
                            @forelse ($openSpaces as $space)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('spaces.show', $space) }}">{{ $space->name }}</a>
                                    <span class="badge bg-secondary">{{ $space->type }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nenhum espaço aberto.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">Últimas despesas</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($latestExpenses as $expense)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div>{{ $expense->description }}</div>
                                        <small class="text-muted">{{ $expense->space->name }} · {{ $expense->expense_date->format('d/m/Y') }}</small>
                                    </div>
                                    <span class="fw-bold">R$ {{ number_format($expense->amount, 2, ',', '.') }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nenhuma despesa registrada.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
