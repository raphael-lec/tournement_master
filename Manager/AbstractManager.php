<?php

abstract class AbstractManager
{
    protected PDO $db;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '';
        $port = !empty($_ENV['DB_PORT']) ? ";port=" . $_ENV['DB_PORT'] : "";
        $connexionString = "mysql:host=$host" . $port . ";dbname=$dbname;charset=utf8";
        $this->db = new PDO($connexionString, $user, $pass);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}