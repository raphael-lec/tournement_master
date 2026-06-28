<?php

class groupe_participant {
    private $user_id;
    private $Groupe_id;
    public function __construct($user_id, $Groupe_id) {
        $this->user_id = $user_id;
        $this->Groupe_id =$Groupe_id;
    }
    public function getUser_id() {
        return $this->user_id;
    }
    public function getGroupe_id() {
        return $this->Groupe_id;
    }
}