<?php

class ExpenseManager extends AbstractManager 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function createExpense(Expenses $expense, array $participantIds): bool
{
    $this->db->beginTransaction();

    
        $query = $this->db->prepare("
            INSERT INTO expenses (title, amount, date, paid_by_id, category_id, groupe_id) 
            VALUES (:title, :amount, :date, :paid_by_id, :category_id, :groupe_id)
        ");
        
        $query->execute([
            'title' => $expense->getTitle(),
            'amount' => $expense->getAmount(),
            'date' => $expense->getDate(),
            'paid_by_id' => $expense->getPaidById(),
            'category_id' => $expense->getCategoryId(),
            'groupe_id' => $expense->getGroupeId(),
        ]);
        
        $expenseId = $this->db->lastInsertId();
        
        if (!$expenseId) {
             throw new \PDOException("Impossible de récupérer l'ID de la dépense.", 999);
        }

        $insertParticipantsQuery = "
            INSERT INTO expense_participants (expense_id, user_id, groupe_id) 
            VALUES (:expense_id, :user_id, :groupe_id)
        ";
        $participantStatement = $this->db->prepare($insertParticipantsQuery);
        
        foreach ($participantIds as $userId) {
            $participantStatement->execute([
                'expense_id' => $expenseId, 
                'user_id' => $userId,
                'groupe_id' => $expense->getGroupeId()
            ]);
        }
        
        $this->db->commit();
        return true;

    }
    
    public function getAllCategories(): array
    {
        $query = $this->db->query("SELECT id, type FROM categories");
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $categories = [];
        foreach($results as $result) {
            $categories[] = new Categories($result['id'], $result['type']);
        }
        return $categories;
    }
    public function getExpensesByGroup(int $groupeId): array
    {
        $query = $this->db->prepare("
            
            SELECT 
                e.id, 
                e.title, 
                e.amount, 
                e.date, 
                u.username AS paid_by_name,
                c.type
                
            FROM expenses e
            JOIN expense_participants ep ON e.id = expense_id
            JOIN users u ON ep.user_id = u.id
            JOIN categories c on c.id = e.category_id
            WHERE e.groupe_id = :groupe_id
            ORDER BY e.date DESC, e.id DESC
        ");
        
        $query->execute([':groupe_id' => $groupeId]);
        $expenses = $query->fetchAll(PDO::FETCH_ASSOC);

        if (empty($expenses)) {
            return [];
        }

        $expenseIds = array_column($expenses, 'id');
        $placeholders = implode(',', array_fill(0, count($expenseIds), '?'));
        
        $participantsQuery = $this->db->prepare("
            SELECT 
                ep.expense_id, 
                u.username 
            FROM expense_participants ep
            JOIN users u ON ep.user_id = u.id
            WHERE ep.expense_id IN ({$placeholders})
        ");

        $participantsQuery->execute($expenseIds);
        $allParticipants = $participantsQuery->fetchAll(PDO::FETCH_ASSOC);
        
        $participantsByExpense = [];
        foreach ($allParticipants as $participant) {
            $participantsByExpense[$participant['expense_id']][] = $participant['username'];
        }

        foreach ($expenses as $key => $expense) {
            $expenses[$key]['participants'] = $participantsByExpense[$expense['id']] ?? [];
        }

        return $expenses;
    }
}
?>