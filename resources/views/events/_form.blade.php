@php
    $startDate = old('start_date', $event ? $event->start_at->format('Y-m-d') : request('date', now()->format('Y-m-d')));
    $startTime = old('start_time', $event && ! $event->all_day ? $event->start_at->format('H:i') : '');
    $endDate = old('end_date', $event && $event->end_at ? $event->end_at->format('Y-m-d') : '');
    $endTime = old('end_time', $event && $event->end_at && ! $event->all_day ? $event->end_at->format('H:i') : '');
    $allDay = old('all_day', $event->all_day ?? false);
    $selectedParticipants = old('participants', $event ? $event->participants->pluck('id')->toArray() : []);
@endphp

<div class="mb-3">
    <label class="form-label">Título</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $event->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Descrição</label>
    <textarea name="description" class="form-control">{{ old('description', $event->description ?? '') }}</textarea>
</div>

<div class="form-check mb-3">
    <input type="checkbox" name="all_day" id="all_day" class="form-check-input" value="1" {{ $allDay ? 'checked' : '' }}>
    <label class="form-check-label" for="all_day">Dia inteiro</label>
</div>

<div class="row">
    <div class="col-md-3 mb-3">
        <label class="form-label">Data de início</label>
        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Hora de início</label>
        <input type="time" name="start_time" class="form-control time-field" value="{{ $startTime }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Data de término (opcional)</label>
        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Hora de término</label>
        <input type="time" name="end_time" class="form-control time-field" value="{{ $endTime }}">
    </div>
</div>

<h6 class="mt-4">Pessoas envolvidas</h6>
<p class="text-muted small">Você (criador) já faz parte do evento automaticamente. Selecione quem mais deve vê-lo no calendário.</p>
<div class="row mb-3">
    @foreach ($users as $user)
        <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" name="participants[]" value="{{ $user->id }}" class="form-check-input" id="participant_{{ $user->id }}" {{ in_array($user->id, $selectedParticipants) ? 'checked' : '' }}>
                <label class="form-check-label" for="participant_{{ $user->id }}">{{ $user->name }}</label>
            </div>
        </div>
    @endforeach
</div>

<script>
    document.getElementById('all_day').addEventListener('change', function () {
        document.querySelectorAll('.time-field').forEach(el => el.closest('.col-md-3').style.display = this.checked ? 'none' : '');
    });
    document.getElementById('all_day').dispatchEvent(new Event('change'));
</script>
