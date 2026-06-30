<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Space;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function store(Request $request, Space $space)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'nullable|in:admin,editor,participante,visualizador',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Usuário não encontrado.']);
        }

        $space->members()->firstOrCreate(
            ['user_id' => $user->id],
            ['role' => $validated['role'] ?? 'participante', 'joined_at' => now()]
        );

        return redirect()->route('spaces.show', $space)->with('status', 'Membro adicionado.');
    }

    public function destroy(Space $space, Member $member)
    {
        abort_unless($member->space_id === $space->id, 404);

        $member->delete();

        return redirect()->route('spaces.show', $space)->with('status', 'Membro removido.');
    }
}
