<?php

require_once("Media.php");

class Movie extends Media{
    private float $duration;
    private string $gender;
    public function __construct(string $titre, string $auteur, bool $disponible, float $duration, string $gender) {
        parent::__construct($titre, $auteur, $disponible);
        $this->duration = $duration;
        $this->gender = $gender;
    }

    public function getDuration(): float {
        return $this->duration;
    }

    public function setDuration(float $duration): void{
        $this->duration = $duration;
    }

    public function getGender(): string {
        return $this->gender;
    }

    public function setGender(string $gender): void{
        $this->gender = $gender;
    }
}
