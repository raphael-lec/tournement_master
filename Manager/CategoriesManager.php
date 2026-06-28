<?php

class CategoryManager extends AbstractManager 
{

    public function getCategoryById(int $id): ?Categories
    {
        $query = $this->db->prepare("
            SELECT id, type 
            FROM categories 
            WHERE id = :id
        ");
        
        $parametres = [
            ":id" => $id
        ];
        
        $query->execute($parametres);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new Categories(
            $result['id'],
            $result['type']
        );
    }
}