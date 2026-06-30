<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Despesas — {{ $space->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('spaces.expenses.create', $space) }}" class="btn btn-primary">Nova despesa</a>
            </div>

            <div class="card">
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
                        @forelse ($expenses as $expense)
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
</x-app-layout>
