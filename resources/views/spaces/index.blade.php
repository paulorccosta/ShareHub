<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Espaços</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('spaces.create') }}" class="btn btn-primary">Novo espaço</a>
            </div>

            <div class="card">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Período</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spaces as $space)
                            <tr>
                                <td><a href="{{ route('spaces.show', $space) }}">{{ $space->name }}</a></td>
                                <td><span class="badge bg-secondary">{{ $space->type }}</span></td>
                                <td>
                                    <span class="badge {{ $space->status === 'aberto' ? 'bg-success' : 'bg-dark' }}">
                                        {{ $space->status }}
                                    </span>
                                </td>
                                <td>
                                    {{ optional($space->start_date)->format('d/m/Y') }}
                                    @if($space->end_date) - {{ $space->end_date->format('d/m/Y') }} @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('spaces.show', $space) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-4">Nenhum espaço encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
