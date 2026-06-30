<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Usuários') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <ul class="nav nav-pills mb-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Visão geral</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.users') }}">Usuários</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.spaces') }}">Espaços</a></li>
                </ul>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Novo usuário</a>
            </div>

            <div class="card">
                <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Espaços (dono)</th>
                            <th>Participações</th>
                            <th>Papel</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->owned_spaces_count }}</td>
                                <td>{{ $user->memberships_count }}</td>
                                <td>
                                    @if ($user->is_admin)
                                        <span class="badge bg-dark">administrador</span>
                                    @else
                                        <span class="badge bg-secondary">usuário</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($user->id !== auth()->id())
                                        @if ($user->is_admin)
                                            <form action="{{ route('admin.users.demote', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary">Remover admin</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.promote', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-dark">Tornar admin</button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">você</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
