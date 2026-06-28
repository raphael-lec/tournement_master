<?php

class GameManager extends AbstractManager // Ou ta classe de base pour les managers
{
    public function addGame(string $name) : void
    {
        // On utilise 'game' car c'est le nom de ta table
        $query = $this->db->prepare("INSERT INTO game (game_name) VALUES (:name)");
        $query->execute(['name' => $name]);
    }
}   