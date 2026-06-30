<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Space $space)
    {
        $expenses = $space->expenses()->with(['user', 'category'])->latest('expense_date')->get();

        return view('expenses.index', compact('space', 'expenses'));
    }

    public function create(Space $space)
    {
        $categories = Category::forSpaceType($space->type)->orderBy('name')->get();
        $members = $space->users()->get();

        return view('expenses.create', compact('space', 'categories', 'members'));
    }

    public function store(Request $request, Space $space)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'split_type' => 'required|in:igual,personalizada,percentual,valor_fixo',
            'status' => 'nullable|in:pendente,confirmado',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'percentages' => 'nullable|array',
            'fixed_values' => 'nullable|array',
        ]);

        $expense = $space->expenses()->create([
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'confirmado',
            'split_type' => $validated['split_type'],
        ]);

        $participants = $validated['participants'];
        $amount = (float) $validated['amount'];
        $count = count($participants);

        $total = 0;
        foreach ($participants as $index => $userId) {
            $percentage = null;
            $fixedValue = null;
            $shareAmount = null;

            if ($validated['split_type'] === 'percentual') {
                $percentage = (float) ($validated['percentages'][$userId] ?? 0);
                $shareAmount = round($amount * $percentage / 100, 2);
            } elseif ($validated['split_type'] === 'valor_fixo') {
                $fixedValue = (float) ($validated['fixed_values'][$userId] ?? 0);
                $shareAmount = round($fixedValue, 2);
            } else {
                $isLast = $index === array_key_last($participants);
                $shareAmount = $isLast ? round($amount - $total, 2) : round($amount / $count, 2);
                $total += $shareAmount;
            }

            $expense->expenseParticipants()->create([
                'user_id' => $userId,
                'percentage' => $percentage,
                'fixed_value' => $fixedValue,
                'share_amount' => $shareAmount,
            ]);
        }

        return redirect()->route('spaces.show', $space)->with('status', 'Despesa registrada.');
    }

    public function show(Space $space, Expense $expense)
    {
        $expense->load(['user', 'category', 'expenseParticipants.user', 'attachments']);

        return view('expenses.show', compact('space', 'expense'));
    }

    public function edit(Space $space, Expense $expense)
    {
        $categories = Category::forSpaceType($space->type)->orderBy('name')->get();
        $members = $space->users()->get();
        $expense->load('expenseParticipants');

        return view('expenses.edit', compact('space', 'expense', 'categories', 'members'));
    }

    public function update(Request $request, Space $space, Expense $expense)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'notes' => 'nullable|string',
            'split_type' => 'required|in:igual,personalizada,percentual,valor_fixo',
            'status' => 'nullable|in:pendente,confirmado',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'percentages' => 'nullable|array',
            'fixed_values' => 'nullable|array',
        ]);

        $expense->update([
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? $expense->status,
            'split_type' => $validated['split_type'],
        ]);

        $expense->expenseParticipants()->delete();

        $participants = $validated['participants'];
        $amount = (float) $validated['amount'];
        $count = count($participants);
        $total = 0;

        foreach ($participants as $index => $userId) {
            $percentage = null;
            $fixedValue = null;
            $shareAmount = null;

            if ($validated['split_type'] === 'percentual') {
                $percentage = (float) ($validated['percentages'][$userId] ?? 0);
                $shareAmount = round($amount * $percentage / 100, 2);
            } elseif ($validated['split_type'] === 'valor_fixo') {
                $fixedValue = (float) ($validated['fixed_values'][$userId] ?? 0);
                $shareAmount = round($fixedValue, 2);
            } else {
                $isLast = $index === array_key_last($participants);
                $shareAmount = $isLast ? round($amount - $total, 2) : round($amount / $count, 2);
                $total += $shareAmount;
            }

            $expense->expenseParticipants()->create([
                'user_id' => $userId,
                'percentage' => $percentage,
                'fixed_value' => $fixedValue,
                'share_amount' => $shareAmount,
            ]);
        }

        return redirect()->route('spaces.show', $space)->with('status', 'Despesa atualizada.');
    }

    public function destroy(Space $space, Expense $expense)
    {
        $expense->delete();

        return redirect()->route('spaces.show', $space)->with('status', 'Despesa removida.');
    }
}
