<?php

class Expense_participant {
    private $expense_id;
    private $user_id;
    private $Groupe_id;
    public function __construct($expense_id, $user_id, $Groupe_id) {
        $this->expense_id = $expense_id;
        $this->user_id = $user_id;
        $this->Groupe_id =$Groupe_id;
    }
    public function getExpense_id() {
        return $this->expense_id;
    }
    public function getUser_id() {
        return $this->user_id;
    }
    public function getGroupe_id() {
        return $this->Groupe_id;
    }
}