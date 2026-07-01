<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar evento</h2>
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

                    <form method="POST" action="{{ route('events.update', $event) }}">
                        @csrf
                        @method('PUT')
                        @include('events._form', ['event' => $event, 'users' => $users])

                        <button type="submit" class="btn btn-primary">Salvar alterações</button>
                        <a href="{{ route('events.index') }}" class="btn btn-link">Voltar</a>
                    </form>

                    <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline" onsubmit="return confirm('Cancelar este evento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger mt-2">Cancelar evento</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
