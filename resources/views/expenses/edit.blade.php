<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Editar despesa — {{ $space->name }}</h2>
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

                    @php
                        $participantIds = $expense->expenseParticipants->pluck('user_id')->all();
                        $byUser = $expense->expenseParticipants->keyBy('user_id');
                    @endphp

                    <form method="POST" action="{{ route('spaces.expenses.update', [$space, $expense]) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description', $expense->description) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Valor (R$)</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Data</label>
                                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected($expense->category_id === $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Forma de divisão</label>
                                <select name="split_type" id="split_type" class="form-select">
                                    @foreach (['igual' => 'Igual entre participantes', 'personalizada' => 'Personalizada', 'percentual' => 'Percentual', 'valor_fixo' => 'Valor fixo'] as $value => $label)
                                        <option value="{{ $value }}" @selected($expense->split_type === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                        </div>

                        <h6 class="mt-4">Participantes</h6>
                        <div class="table-responsive">
                                                <table class="table">
                            <thead>
                                <tr>
                                    <th>Participa</th>
                                    <th>Nome</th>
                                    <th class="pct-col" @style(['display:none' => $expense->split_type !== 'percentual'])>%</th>
                                    <th class="fixed-col" @style(['display:none' => $expense->split_type !== 'valor_fixo'])>Valor fixo (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td><input type="checkbox" name="participants[]" value="{{ $member->id }}" @checked(in_array($member->id, $participantIds))></td>
                                        <td>{{ $member->name }}</td>
                                        <td class="pct-col" @style(['display:none' => $expense->split_type !== 'percentual'])>
                                            <input type="number" step="0.01" min="0" max="100" name="percentages[{{ $member->id }}]" class="form-control form-control-sm" value="{{ optional($byUser->get($member->id))->percentage }}">
                                        </td>
                                        <td class="fixed-col" @style(['display:none' => $expense->split_type !== 'valor_fixo'])>
                                            <input type="number" step="0.01" min="0" name="fixed_values[{{ $member->id }}]" class="form-control form-control-sm" value="{{ optional($byUser->get($member->id))->fixed_value }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>

                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="{{ route('spaces.show', $space) }}" class="btn btn-link">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('split_type').addEventListener('change', function () {
            const pctCols = document.querySelectorAll('.pct-col');
            const fixedCols = document.querySelectorAll('.fixed-col');
            pctCols.forEach(el => el.style.display = this.value === 'percentual' ? '' : 'none');
            fixedCols.forEach(el => el.style.display = this.value === 'valor_fixo' ? '' : 'none');
        });
    </script>
</x-app-layout>
