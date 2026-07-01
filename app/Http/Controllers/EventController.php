<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $month = Carbon::parse($request->get('month', now()->format('Y-m')).'-01');
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $events = Auth::user()->events()
            ->whereBetween('start_at', [$monthStart, $monthEnd])
            ->orderBy('start_at')
            ->get()
            ->groupBy(fn (Event $event) => $event->start_at->format('Y-m-d'));

        DB::table('event_participants')
            ->where('user_id', Auth::id())
            ->whereNull('viewed_at')
            ->update(['viewed_at' => now()]);

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        return view('events.index', compact('events', 'month', 'calendarStart', 'calendarEnd'));
    }

    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();

        return view('events.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        [$startAt, $endAt] = $this->resolveDates($validated, $request->boolean('all_day'));

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'all_day' => $request->boolean('all_day'),
        ]);

        $participantIds = array_unique(array_merge($validated['participants'] ?? [], [Auth::id()]));
        $pivotData = [];
        foreach ($participantIds as $id) {
            $pivotData[$id] = ['viewed_at' => $id == Auth::id() ? now() : null];
        }
        $event->participants()->sync($pivotData);

        return redirect()->route('events.index', ['month' => $event->start_at->format('Y-m')])->with('status', 'Evento criado.');
    }

    public function edit(Event $event)
    {
        abort_unless($event->isParticipant(Auth::id()), 403);

        $event->load('participants');
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();

        return view('events.edit', compact('event', 'users'));
    }

    public function update(Request $request, Event $event)
    {
        abort_unless($event->isParticipant(Auth::id()), 403);

        $validated = $this->validateEvent($request);

        [$startAt, $endAt] = $this->resolveDates($validated, $request->boolean('all_day'));

        $event->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'all_day' => $request->boolean('all_day'),
        ]);

        $participantIds = array_unique(array_merge($validated['participants'] ?? [], [Auth::id()]));
        $existingViewed = $event->participants()->pluck('event_participants.viewed_at', 'users.id');

        $pivotData = [];
        foreach ($participantIds as $id) {
            $pivotData[$id] = ['viewed_at' => $existingViewed[$id] ?? ($id == Auth::id() ? now() : null)];
        }
        $event->participants()->sync($pivotData);

        return redirect()->route('events.index', ['month' => $event->start_at->format('Y-m')])->with('status', 'Evento atualizado.');
    }

    public function destroy(Event $event)
    {
        abort_unless($event->isParticipant(Auth::id()), 403);

        $month = $event->start_at->format('Y-m');
        $event->delete();

        return redirect()->route('events.index', ['month' => $month])->with('status', 'Evento cancelado.');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'all_day' => 'nullable|boolean',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);
    }

    private function resolveDates(array $validated, bool $allDay): array
    {
        $startAt = $allDay
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::parse($validated['start_date'].' '.($validated['start_time'] ?? '00:00'));

        $endAt = null;
        if (! empty($validated['end_date'])) {
            $endAt = $allDay
                ? Carbon::parse($validated['end_date'])->endOfDay()
                : Carbon::parse($validated['end_date'].' '.($validated['end_time'] ?? $validated['start_time'] ?? '00:00'));
        }

        return [$startAt, $endAt];
    }
}
