<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Space;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'spacesCount' => Space::count(),
            'expensesCount' => Expense::count(),
            'totalSpent' => Expense::sum('amount'),
            'latestUsers' => User::latest()->take(5)->get(),
            'latestSpaces' => Space::with('owner')->latest()->take(5)->get(),
        ]);
    }

    public function users()
    {
        $users = User::withCount(['ownedSpaces', 'memberships'])->orderBy('name')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function createUserForm()
    {
        return view('admin.users-create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_admin' => 'sometimes|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if ($request->boolean('is_admin')) {
            $user->forceFill(['is_admin' => true])->save();
        }

        return redirect()->route('admin.users')->with('status', "Usuário {$user->name} criado.");
    }

    public function promoteUser(Request $request, User $user)
    {
        $user->forceFill(['is_admin' => true])->save();

        return back()->with('status', "{$user->name} agora é administrador.");
    }

    public function demoteUser(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Você não pode remover seu próprio acesso de administrador.');

        $user->forceFill(['is_admin' => false])->save();

        return back()->with('status', "{$user->name} não é mais administrador.");
    }

    public function destroyUser(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 403, 'Você não pode excluir seu próprio usuário.');

        $user->delete();

        return back()->with('status', 'Usuário removido.');
    }

    public function spaces()
    {
        $spaces = Space::with('owner')->withCount(['members', 'expenses'])->orderBy('name')->paginate(20);

        return view('admin.spaces', compact('spaces'));
    }

    public function destroySpace(Space $space)
    {
        $space->delete();

        return back()->with('status', 'Espaço removido.');
    }
}
