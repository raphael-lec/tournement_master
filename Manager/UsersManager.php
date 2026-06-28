<?php

class UsersManager extends AbstractManager
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function register(string $email, string $password, string $username): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = $this->db->prepare("
            INSERT INTO users (email, password, name, type) 
            VALUES (:email, :password, :name, :type)
        ");
        
        return $query->execute([
            'email'    => $email,
            'password' => $hashedPassword,
            'name'     => $username, 
            'type'     => 'user'
        ]);
    }
    
    public function login(string $email, string $plainPassword): ?User
    {
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

    public function getUserById(int $id) : User
    {
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

    public function getAllUsers() : array
    {
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
                $result["name"], 
                $result["type"]
            );
        }
        
        return $users;
    }
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
public function updateProfile(int $id, string $name, string $email) : bool
{
    $query = $this->db->prepare("
        UPDATE users SET name = :name, email = :email WHERE id = :id
    ");
    return $query->execute(['name' => $name, 'email' => $email, 'id' => $id]);
}
}