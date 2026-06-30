<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Espaços') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <ul class="nav nav-pills mb-4">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Visão geral</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">Usuários</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.spaces') }}">Espaços</a></li>
            </ul>

            <div class="card">
                <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Dono</th>
                            <th>Membros</th>
                            <th>Despesas</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spaces as $space)
                            <tr>
                                <td><a href="{{ route('spaces.show', $space) }}">{{ $space->name }}</a></td>
                                <td><span class="badge bg-secondary">{{ $space->type }}</span></td>
                                <td>{{ $space->owner?->name }}</td>
                                <td>{{ $space->members_count }}</td>
                                <td>{{ $space->expenses_count }}</td>
                                <td>{{ $space->status }}</td>
                                <td class="text-end">
                                    <form action="{{ route('admin.spaces.destroy', $space) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este espaço e todos os seus dados?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $spaces->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
