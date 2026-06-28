<?php

class UsersManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }
    
    // ==========================================
    // ➕ INSCRIPTION (REGISTER)
    // ==========================================
    public function register(string $email, string $password, string $username): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // 🔄 Remplacement de username par name
        $query = $this->db->prepare("
            INSERT INTO users (email, password, name, type) 
            VALUES (:email, :password, :name, :type)
        ");
        
        return $query->execute([
            'email'    => $email,
            'password' => $hashedPassword,
            'name'     => $username, // On stocke la valeur reçue dans la colonne 'name'
            'type'     => 'user'
        ]);
    }
    
    // ==========================================
    // 🔑 CONNEXION (LOGIN)
    // ==========================================
    public function login(string $email, string $plainPassword): ?User
    {
        // 🔄 Sélection de name au lieu de username
        $query = $this->db->prepare("SELECT id, email, password, name, type FROM users WHERE email = :email");
        $query->execute(['email' => $email]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($plainPassword, $result['password'])) {
            
            return new User(
                $result["id"], 
                $result["email"], 
                $result["password"], 
                $result["name"], // 🔄 name
                $result["type"]
            );
        }

        return null;
    }

    // ==========================================
    // 🔍 RÉCUPÉRER UN UTILISATEUR PAR SON ID
    // ==========================================
    public function getUserById(int $id) : User
    {
        // 🔄 Sélection de name au lieu de username
        $query = $this->db->prepare("SELECT id, email, password, name, type FROM users WHERE id = :id");
        $query->execute(['id' => $id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return new User(
            $result["id"],
            $result["email"],
            $result["password"],
            $result["name"], // 🔄 name
            $result["type"]
        );
    }

    // ==========================================
    // 📋 RÉCUPÉRER TOUS LES UTILISATEURS
    // ==========================================
    public function getAllUsers() : array
    {
        // 🔄 Sélection de name au lieu de username
        $query = $this->db->prepare("SELECT id, email, password, name, type FROM users");
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $users = [];

        foreach($results as $result)
        {
            $users[] = new User(
                $result["id"],
                $result["email"],
                $result["password"],
                $result["name"], // 🔄 name
                $result["type"]
            );
        }
        
        return $users;
    }
    // ==========================================
    // ❌ SUPPRIMER UN UTILISATEUR
    // ==========================================
    public function deleteUser(int $id) : bool
    {
        $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $query->execute(['id' => $id]);
    }
    public function updateUserType(int $id, string $type) : bool
{
    $query = $this->db->prepare("UPDATE users SET type = :type WHERE id = :id");
    return $query->execute([
        'type' => $type,
        'id'   => $id
    ]);
}
}