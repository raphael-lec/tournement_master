<?php

class Reimbursements {
    private $id;
    private $amount;
    private $date;
    private $user_from_id;
    private $user_to_id;
    private $groupe_id;
    public function __construct($id, $amount, $date, $user_from_id, $user_to_id, $groupe_id) {
        $this->id = $id;
        $this->amount = $amount;
        $this->date = $date;
        $this->user_from_id = $user_from_id;
        $this->user_to_id = $user_to_id;
        $this->groupe_id = $groupe_id;
    }
    public function getId() {
        return $this->id;
    }
    public function getAmount() {
        return $this->amount;
    }
    public function getDate() {
        return $this->date;
    }
    public function getUser_from_id() {
        return $this->user_from_id;
    }
    public function getUser_to_id() {
        return $this->user_to_id;
    }
    public function getGroupe_id() {
        return $this->groupe_id;
    }
}