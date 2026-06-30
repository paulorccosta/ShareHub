<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Novo usuário') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            <ul class="nav nav-pills mb-4">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Visão geral</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.users') }}">Usuários</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.spaces') }}">Espaços</a></li>
            </ul>

            <div class="card" style="max-width: 480px;">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirmar senha</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_admin" value="1" class="form-check-input" id="isAdmin">
                            <label class="form-check-label" for="isAdmin">Tornar administrador</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Criar usuário</button>
                        <a href="{{ route('admin.users') }}" class="btn btn-link">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
