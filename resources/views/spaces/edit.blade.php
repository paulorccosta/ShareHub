<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar espaço</h2>
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

                    <form method="POST" action="{{ route('spaces.update', $space) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $space->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control">{{ old('description', $space->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="type" class="form-select" required>
                                    @foreach (['CASA', 'EVENTO', 'VIAGEM', 'OUTRO'] as $type)
                                        <option value="{{ $type }}" @selected(old('type', $space->type) === $type)>{{ ucfirst(strtolower($type)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="aberto" @selected(old('status', $space->status) === 'aberto')>Aberto</option>
                                    <option value="fechado" @selected(old('status', $space->status) === 'fechado')>Fechado</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cor</label>
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $space->color ?? '#0d6efd') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data início</label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', optional($space->start_date)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data fim</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', optional($space->end_date)->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="{{ route('spaces.show', $space) }}" class="btn btn-link">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
