<?php

class Groupe {
    private int $id;
    private string $name;
    private int $budget = 0;
    private string $code;
    public function __construct($id, $name, $code, $budget) {
        $this->id = $id;
        $this->name = $name;
        $this->budget = $budget;
        $this->code = $code;
    }
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getBudget() {
        return $this->budget;
    }
    public function getCode() {
        return $this->code;
    }
    public function addBudjet($ajout) {
        $this->budget += $ajout;
    }
}