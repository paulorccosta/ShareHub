<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $space->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-secondary">{{ $space->type }}</span>
                    <span class="badge {{ $space->status === 'aberto' ? 'bg-success' : 'bg-dark' }}">{{ $space->status }}</span>
                    @if ($space->description)
                        <p class="mt-2 mb-0">{{ $space->description }}</p>
                    @endif
                </div>
                <div class="text-end">
                    <a href="{{ route('rateio.show', $space) }}" class="btn btn-info">Ver rateio</a>
                    <a href="{{ route('spaces.edit', $space) }}" class="btn btn-outline-secondary">Editar</a>
                    <form action="{{ route('spaces.destroy', $space) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover este espaço?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">Excluir</button>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">Membros</div>
                        <ul class="list-group list-group-flush">
                            @foreach ($space->members as $member)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $member->user->name }}
                                        <span class="badge bg-light text-dark">{{ $member->role }}</span>
                                    </div>
                                    @if ($member->user_id !== $space->owner_id)
                                        <form action="{{ route('spaces.members.destroy', [$space, $member]) }}" method="POST" onsubmit="return confirm('Remover membro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">x</button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="card-body">
                            <form action="{{ route('spaces.members.store', $space) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                                    <button class="btn btn-primary">Adicionar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Despesas</span>
                            <a href="{{ route('spaces.expenses.create', $space) }}" class="btn btn-sm btn-primary">Nova despesa</a>
                        </div>
                        <div class="table-responsive">
                                                <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Categoria</th>
                                    <th>Pago por</th>
                                    <th>Data</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($space->expenses as $expense)
                                    <tr>
                                        <td><a href="{{ route('spaces.expenses.show', [$space, $expense]) }}">{{ $expense->description }}</a></td>
                                        <td>{{ $expense->category->name ?? '-' }}</td>
                                        <td>{{ $expense->user->name }}</td>
                                        <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                        <td class="text-end">R$ {{ number_format($expense->amount, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted text-center py-4">Nenhuma despesa registrada.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
