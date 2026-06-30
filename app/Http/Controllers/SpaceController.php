<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpaceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $spaces = Space::where('owner_id', $user->id)
            ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->latest()
            ->get();

        return view('spaces.index', compact('spaces'));
    }

    public function create()
    {
        return view('spaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:CASA,EVENTO,VIAGEM,OUTRO',
            'status' => 'nullable|in:aberto,fechado',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'aberto';

        $space = Space::create($validated);

        $space->members()->create([
            'user_id' => Auth::id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return redirect()->route('spaces.show', $space)->with('status', 'Espaço criado com sucesso.');
    }

    public function show(Space $space)
    {
        $this->authorizeMember($space);

        $space->load(['members.user', 'owner', 'expenses' => function ($q) {
            $q->with(['user', 'category'])->latest('expense_date');
        }]);

        return view('spaces.show', compact('space'));
    }

    public function edit(Space $space)
    {
        $this->authorizeMember($space);

        return view('spaces.edit', compact('space'));
    }

    public function update(Request $request, Space $space)
    {
        $this->authorizeMember($space);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:CASA,EVENTO,VIAGEM,OUTRO',
            'status' => 'nullable|in:aberto,fechado',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        $space->update($validated);

        return redirect()->route('spaces.show', $space)->with('status', 'Espaço atualizado.');
    }

    public function destroy(Space $space)
    {
        $this->authorizeMember($space);

        $space->delete();

        return redirect()->route('spaces.index')->with('status', 'Espaço removido.');
    }

    private function authorizeMember(Space $space): void
    {
        abort_unless($space->isMember(Auth::id()), 403);
    }
}
