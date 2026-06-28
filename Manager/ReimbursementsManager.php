<?php

class ReimbursementsManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }
    public function setReimbursement(float $amount, int $user_from_id, int $user_to_id, int $groupe_id): bool
    {
        // On prépare la date actuelle au format YYYY-MM-DD HH:MM:SS
        $date = date('Y-m-d H:i:s'); 
        
        $query = $this->db->prepare("
            INSERT INTO reimbursements 
                (amount, date, user_from_id, user_to_id, groupe_id) 
            VALUES 
                (:amount, :date, :user_from_id, :user_to_id, :groupe_id)
        ");
        
        $success = $query->execute([
            'amount' => $amount,
            'date' => $date, 
            'user_from_id' => $user_from_id,
            'user_to_id' => $user_to_id,
            'groupe_id' => $groupe_id,
        ]);
        
        return $success;
    }
    public function getAllReimbursementsByGroup(int $groupeId) : array
        {
        $query = $this->db->prepare("
            SELECT id, amount, date, user_from_id, user_to_id, groupe_id 
            FROM reimbursements
            WHERE groupe_id = :groupe_id;
        ");

        $query->execute([
            ":groupe_id" => $groupeId
        ]);

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $remboursements = [];

        foreach($results as $result)
        {
            $remboursements[] = new Reimbursements(
                $result["id"], 
                $result["amount"], 
                $result["date"], 
                $result["user_from_id"], 
                $result["user_to_id"], 
                $result["groupe_id"]
            );
        }

        return $remboursements;
        }
}
?>