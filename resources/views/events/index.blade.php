<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">Calendário</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="btn-group">
                            <a href="{{ route('events.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">&laquo;</a>
                            <a href="{{ route('events.index', ['month' => now()->format('Y-m')]) }}" class="btn btn-outline-secondary">Hoje</a>
                            <a href="{{ route('events.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-outline-secondary">&raquo;</a>
                        </div>
                        <h5 class="mb-0">{{ ucfirst($month->translatedFormat('F \d\e Y')) }}</h5>
                        <a href="{{ route('events.create', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-primary">+ Novo evento</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-top">
                            <thead>
                                <tr class="text-center">
                                    <th>Dom</th>
                                    <th>Seg</th>
                                    <th>Ter</th>
                                    <th>Qua</th>
                                    <th>Qui</th>
                                    <th>Sex</th>
                                    <th>Sáb</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $day = $calendarStart->copy(); @endphp
                                @while ($day <= $calendarEnd)
                                    <tr>
                                        @for ($i = 0; $i < 7; $i++)
                                            @php
                                                $dateKey = $day->format('Y-m-d');
                                                $isCurrentMonth = $day->month === $month->month;
                                                $isToday = $day->isToday();
                                                $dayEvents = $events->get($dateKey, collect());
                                            @endphp
                                            <td style="width: 14.28%; height: 110px;" class="{{ $isCurrentMonth ? '' : 'bg-light text-muted' }} {{ $isToday ? 'border-primary border-2' : '' }}">
                                                <div class="d-flex justify-content-between">
                                                    <small>{{ $day->day }}</small>
                                                    <a href="{{ route('events.create', ['date' => $dateKey]) }}" class="text-decoration-none small" title="Novo evento neste dia">+</a>
                                                </div>
                                                @foreach ($dayEvents as $event)
                                                    <a href="{{ route('events.edit', $event) }}" class="d-block text-truncate small badge bg-primary text-wrap text-start mb-1" style="max-width: 100%;">
                                                        @if (! $event->all_day)
                                                            {{ $event->start_at->format('H:i') }}
                                                        @endif
                                                        {{ $event->title }}
                                                    </a>
                                                @endforeach
                                            </td>
                                            @php $day->addDay(); @endphp
                                        @endfor
                                    </tr>
                                @endwhile
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
