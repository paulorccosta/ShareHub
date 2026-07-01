<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Space;
use App\Services\RateioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(RateioService $rateioService)
    {
        $user = Auth::user();

        $spaceIds = Space::query()
            ->where('owner_id', $user->id)
            ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->pluck('id');

        $spaces = Space::whereIn('id', $spaceIds)->get();

        $totalSpaces = $spaces->count();
        $openSpaces = $spaces->where('status', 'aberto');
        $openEventsCount = $openSpaces->count();

        $monthTotal = Expense::whereIn('space_id', $spaceIds)
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $overallBalance = 0.0;
        foreach ($spaces as $space) {
            $result = $rateioService->calcular($space);
            foreach ($result['balances'] as $balance) {
                if ($balance['user_id'] === $user->id) {
                    $overallBalance += $balance['balance'];
                }
            }
        }

        $latestExpenses = Expense::with(['space', 'user', 'category'])
            ->whereIn('space_id', $spaceIds)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $upcomingEvents = $user->events()
            ->where('start_at', '>=', now())
            ->orderBy('start_at')
            ->limit(3)
            ->get();

        return view('dashboard', [
            'totalSpaces' => $totalSpaces,
            'openEventsCount' => $openEventsCount,
            'monthTotal' => $monthTotal,
            'overallBalance' => $overallBalance,
            'openSpaces' => $openSpaces,
            'latestExpenses' => $latestExpenses,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }
}
