<?php

namespace App\Services;

use App\Models\Space;

class RateioService
{
    /**
     * Calculate balances and the minimal set of settlement transactions for a space.
     *
     * @return array{balances: array<int, array{user_id: int, name: string, balance: float}>, transactions: array<int, array{from_user_id: int, from_name: string, to_user_id: int, to_name: string, amount: float}>}
     */
    public function calcular(Space $space): array
    {
        $space->loadMissing(['expenses.expenseParticipants', 'expenses.user', 'users']);

        // user_id => ['paid' => float, 'owed' => float]
        $ledger = [];
        $names = [];

        foreach ($space->users as $user) {
            $ledger[$user->id] = ['paid' => 0.0, 'owed' => 0.0];
            $names[$user->id] = $user->name;
        }

        // Owner might not be in members list explicitly; make sure they're tracked too.
        if ($space->owner && ! isset($ledger[$space->owner->id])) {
            $ledger[$space->owner->id] = ['paid' => 0.0, 'owed' => 0.0];
            $names[$space->owner->id] = $space->owner->name;
        }

        foreach ($space->expenses as $expense) {
            $payerId = $expense->user_id;

            if (! isset($ledger[$payerId])) {
                $ledger[$payerId] = ['paid' => 0.0, 'owed' => 0.0];
                $names[$payerId] = $expense->user->name ?? ('Usuário #'.$payerId);
            }

            $ledger[$payerId]['paid'] += (float) $expense->amount;

            $participants = $expense->expenseParticipants;

            if ($participants->isEmpty()) {
                continue;
            }

            $shares = $this->computeShares($expense, $participants);

            foreach ($shares as $userId => $shareAmount) {
                if (! isset($ledger[$userId])) {
                    $ledger[$userId] = ['paid' => 0.0, 'owed' => 0.0];
                    $names[$userId] = $names[$userId] ?? ('Usuário #'.$userId);
                }

                $ledger[$userId]['owed'] += $shareAmount;
            }
        }

        $balances = [];
        foreach ($ledger as $userId => $values) {
            $balances[] = [
                'user_id' => $userId,
                'name' => $names[$userId] ?? ('Usuário #'.$userId),
                'balance' => round($values['paid'] - $values['owed'], 2),
            ];
        }

        $transactions = $this->simplifyDebts($balances);

        return [
            'balances' => $balances,
            'transactions' => $transactions,
        ];
    }

    /**
     * Compute each participant's share amount for a given expense based on its split_type.
     *
     * @return array<int, float> user_id => share amount
     */
    private function computeShares($expense, $participants): array
    {
        $amount = (float) $expense->amount;
        $splitType = $expense->split_type ?? 'igual';
        $shares = [];

        switch ($splitType) {
            case 'percentual':
                foreach ($participants as $participant) {
                    $pct = (float) ($participant->percentage ?? 0);
                    $shares[$participant->user_id] = round($amount * $pct / 100, 2);
                }
                break;

            case 'valor_fixo':
                foreach ($participants as $participant) {
                    $shares[$participant->user_id] = round((float) ($participant->fixed_value ?? 0), 2);
                }
                break;

            case 'personalizada':
            case 'igual':
            default:
                $count = $participants->count();
                if ($count === 0) {
                    break;
                }
                $equalShare = round($amount / $count, 2);
                $i = 0;
                $total = 0;
                foreach ($participants as $participant) {
                    $i++;
                    // Give any rounding remainder to the last participant.
                    if ($i === $count) {
                        $shares[$participant->user_id] = round($amount - $total, 2);
                    } else {
                        $shares[$participant->user_id] = $equalShare;
                        $total += $equalShare;
                    }
                }
                break;
        }

        return $shares;
    }

    /**
     * Classic greedy debt-simplification: match largest debtor to largest creditor repeatedly.
     *
     * @param array<int, array{user_id: int, name: string, balance: float}> $balances
     * @return array<int, array{from_user_id: int, from_name: string, to_user_id: int, to_name: string, amount: float}>
     */
    private function simplifyDebts(array $balances): array
    {
        $epsilon = 0.005;

        $creditors = [];
        $debtors = [];

        foreach ($balances as $b) {
            if ($b['balance'] > $epsilon) {
                $creditors[] = $b;
            } elseif ($b['balance'] < -$epsilon) {
                $debtors[] = $b;
            }
        }

        usort($creditors, fn ($a, $b) => $b['balance'] <=> $a['balance']);
        usort($debtors, fn ($a, $b) => $a['balance'] <=> $b['balance']); // most negative first

        $transactions = [];

        $i = 0;
        $j = 0;

        while ($i < count($debtors) && $j < count($creditors)) {
            $debtor = $debtors[$i];
            $creditor = $creditors[$j];

            $debtAmount = -$debtor['balance'];
            $creditAmount = $creditor['balance'];

            $settled = round(min($debtAmount, $creditAmount), 2);

            if ($settled > $epsilon) {
                $transactions[] = [
                    'from_user_id' => $debtor['user_id'],
                    'from_name' => $debtor['name'],
                    'to_user_id' => $creditor['user_id'],
                    'to_name' => $creditor['name'],
                    'amount' => $settled,
                ];
            }

            $debtors[$i]['balance'] += $settled;
            $creditors[$j]['balance'] -= $settled;

            if (abs($debtors[$i]['balance']) < $epsilon) {
                $i++;
            }
            if (abs($creditors[$j]['balance']) < $epsilon) {
                $j++;
            }
        }

        return $transactions;
    }
}
