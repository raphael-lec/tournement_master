<?php

abstract class AbstractManager
{
    protected PDO $db;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        
        // 🔑 Sécurité : On teste si 'DB_PASSWORD' ou 'DB_PASS' existe dans le .env
        $pass = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
        
        // 🔌 Gestion du port (si vide, MySQL prendra le port 3306 par défaut)
        $port = !empty($_ENV['DB_PORT']) ? ";port=" . $_ENV['DB_PORT'] : "";
        
        // Construction propre de la chaîne de connexion
        $connexionString = "mysql:host=$host" . $port . ";dbname=$dbname;charset=utf8";

        // Création de l'instance PDO
        $this->db = new PDO($connexionString, $user, $pass);
        
        // 🔥 LA LIGNE INDISPENSABLE : Force PDO à afficher les erreurs si une requête échoue
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}