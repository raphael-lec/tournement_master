<?php

class GameManager extends AbstractManager 
{
    public function addGame(string $name) : void
    {
        $query = $this->db->prepare("INSERT INTO game (game_name) VALUES (:name)");
        $query->execute(['name' => $name]);
    }
    public function getAllGames() : array
    {
        $query = $this->db->query("SELECT id, game_name FROM game ORDER BY game_name");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}   