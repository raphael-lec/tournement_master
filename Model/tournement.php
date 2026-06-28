<?php

class tournement {
    private $id;
    private $game_id;
    private $owner;
    private $date;
    private $max_participant;
    private $status_id;
    private $format;
    private $name;
    public function __construct($id, $game_id, $owner, $date, $max_participant, $status_id, $format, $name) {
        $this->id = $id;
        $this->game_id = $game_id;
        $this->owner = $owner;
        $this->date = $date;
        $this->max_participant = $max_participant;
        $this->status_id = $status_id;
        $this->format = $format;
        $this->name = $name;
    }
    public function getId() {
        return $this->id;
    }
    public function getGameId() {
        return $this->game_id;
    }
    public function getOwner() {
        return $this->owner;
    }
    public function getDate() {
        return $this->date;
    }
    public function getMaxParticipant() {
        return $this->max_participant;
    }
    public function getStatusId() {
        return $this->status_id;
    }
    public function getFormat() {
        return $this->format;
    }
    public function getName() {
        return $this->name;
    }
}