<?php

class Categories {
    private $id;
    private $gamename;
    public function __construct($id, $gamename) {
        $this->id = $id;
        $this->gamename = $gamename;
    }
    public function getId() {
        return $this->id;
    }
    public function getGameName() {
        return $this->gamename;
    }
}