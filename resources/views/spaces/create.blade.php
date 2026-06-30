<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo espaço</h2>
    </x-slot>

    <div class="py-6">
        <div class="container">
            <div class="card">
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

                    <form method="POST" action="{{ route('spaces.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="type" class="form-select" required>
                                    <option value="CASA">Casa</option>
                                    <option value="EVENTO">Evento</option>
                                    <option value="VIAGEM">Viagem</option>
                                    <option value="OUTRO">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="aberto">Aberto</option>
                                    <option value="fechado">Fechado</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cor</label>
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#0d6efd') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data início</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data fim</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Criar espaço</button>
                        <a href="{{ route('spaces.index') }}" class="btn btn-link">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
