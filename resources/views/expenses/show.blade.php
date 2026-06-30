<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $expense->description }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Espaço</dt>
                        <dd class="col-sm-9">{{ $space->name }}</dd>

                        <dt class="col-sm-3">Valor</dt>
                        <dd class="col-sm-9">R$ {{ number_format($expense->amount, 2, ',', '.') }}</dd>

                        <dt class="col-sm-3">Data</dt>
                        <dd class="col-sm-9">{{ $expense->expense_date->format('d/m/Y') }}</dd>

                        <dt class="col-sm-3">Categoria</dt>
                        <dd class="col-sm-9">{{ $expense->category->name ?? '-' }}</dd>

                        <dt class="col-sm-3">Pago por</dt>
                        <dd class="col-sm-9">{{ $expense->user->name }}</dd>

                        <dt class="col-sm-3">Forma de divisão</dt>
                        <dd class="col-sm-9"><span class="badge bg-secondary">{{ $expense->split_type }}</span></dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9"><span class="badge bg-info">{{ $expense->status }}</span></dd>

                        @if ($expense->notes)
                            <dt class="col-sm-3">Observações</dt>
                            <dd class="col-sm-9">{{ $expense->notes }}</dd>
                        @endif
                    </dl>

                    <h6>Participantes</h6>
                    <div class="table-responsive">
                                        <table class="table">
                        <thead>
                            <tr><th>Nome</th><th class="text-end">Parte devida</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($expense->expenseParticipants as $participant)
                                <tr>
                                    <td>{{ $participant->user->name }}</td>
                                    <td class="text-end">R$ {{ number_format($participant->share_amount, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                    <a href="{{ route('spaces.expenses.edit', [$space, $expense]) }}" class="btn btn-outline-secondary">Editar</a>
                    <form action="{{ route('spaces.expenses.destroy', [$space, $expense]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover esta despesa?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger">Excluir</button>
                    </form>
                    <a href="{{ route('spaces.show', $space) }}" class="btn btn-link">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
