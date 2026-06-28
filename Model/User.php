<?php

class User {
    private $id;
    private $email;
    private $password;
    private $name;
    private $created_at;
    private $type;
    public function __construct($id, $email, $password, $name, $type) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->type = $type;
    }
    public function getId() {
        return $this->id;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPassword() {
        return $this->password;
    }
    public function getName(): string {
    return $this->name; }
    public function getType() {
        return $this->type;
    }   
}