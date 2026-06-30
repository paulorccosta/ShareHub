<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Nova despesa — {{ $space->name }}</h2>
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

                    <form method="POST" action="{{ route('spaces.expenses.store', $space) }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Valor (R$)</label>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Data</label>
                                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Forma de divisão</label>
                                <select name="split_type" id="split_type" class="form-select">
                                    <option value="igual">Igual entre participantes</option>
                                    <option value="personalizada">Personalizada (igual entre selecionados)</option>
                                    <option value="percentual">Percentual</option>
                                    <option value="valor_fixo">Valor fixo</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <h6 class="mt-4">Participantes</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Participa</th>
                                    <th>Nome</th>
                                    <th class="pct-col" style="display:none">% </th>
                                    <th class="fixed-col" style="display:none">Valor fixo (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td><input type="checkbox" name="participants[]" value="{{ $member->id }}" checked></td>
                                        <td>{{ $member->name }}</td>
                                        <td class="pct-col" style="display:none">
                                            <input type="number" step="0.01" min="0" max="100" name="percentages[{{ $member->id }}]" class="form-control form-control-sm">
                                        </td>
                                        <td class="fixed-col" style="display:none">
                                            <input type="number" step="0.01" min="0" name="fixed_values[{{ $member->id }}]" class="form-control form-control-sm">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <button type="submit" class="btn btn-primary">Salvar despesa</button>
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
