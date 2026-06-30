<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Painel do Administrador') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <ul class="nav nav-pills mb-4">
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">Visão geral</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Usuários</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.spaces') }}">Espaços</a></li>
            </ul>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-primary h-100">
                        <div class="card-body">
                            <div class="small">Usuários</div>
                            <div class="fs-2 fw-bold">{{ $usersCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-success h-100">
                        <div class="card-body">
                            <div class="small">Espaços</div>
                            <div class="fs-2 fw-bold">{{ $spacesCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-warning h-100">
                        <div class="card-body">
                            <div class="small">Despesas registradas</div>
                            <div class="fs-2 fw-bold">{{ $expensesCount }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card text-bg-info h-100">
                        <div class="card-body">
                            <div class="small">Total movimentado</div>
                            <div class="fs-2 fw-bold">R$ {{ number_format($totalSpent, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">Últimos usuários cadastrados</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($latestUsers as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $user->name }}
                                        @if ($user->is_admin)
                                            <span class="badge bg-dark ms-1">admin</span>
                                        @endif
                                        <br><small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nenhum usuário.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">Últimos espaços criados</div>
                        <ul class="list-group list-group-flush">
                            @forelse ($latestSpaces as $space)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $space->name }}
                                        <br><small class="text-muted">dono: {{ $space->owner?->name }}</small>
                                    </div>
                                    <span class="badge bg-secondary">{{ $space->type }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-muted">Nenhum espaço.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
