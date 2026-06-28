<?php

class BalanceManager extends AbstractManager 
{
 
    public function calculateGroupBalances(int $groupeId): array
    {
        $paidQuery = $this->db->prepare("
            SELECT paid_by_id, SUM(amount) AS total_paid
            FROM expenses
            WHERE groupe_id = :groupe_id
            GROUP BY paid_by_id
        ");
        $paidQuery->execute([':groupe_id' => $groupeId]);
        $totalPaid = $paidQuery->fetchAll(PDO::FETCH_KEY_PAIR);

        $owedQuery = $this->db->prepare("
            SELECT 
                ep.user_id, 
                SUM(e.amount / (
                    SELECT COUNT(user_id) FROM expense_participants WHERE expense_id = e.id
                )) AS total_owed
            FROM expense_participants ep
            JOIN expenses e ON ep.expense_id = e.id
            WHERE ep.groupe_id = :groupe_id
            GROUP BY ep.user_id
        ");
        $owedQuery->execute([':groupe_id' => $groupeId]);
        $totalOwed = $owedQuery->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $balances = [];
        $groupMembers = (new GroupeManager())->getGroupParticipants($groupeId);

        foreach ($groupMembers as $member) {
            $userId = $member->getId();
            $paid = $totalPaid[$userId] ?? 0.0;
            $owed = $totalOwed[$userId] ?? 0.0;
            
            $balances[$userId] = round($paid - $owed, 2);
        }

        return $balances;
    }
}