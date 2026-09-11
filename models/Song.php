<?php

require_once("Album.php");

class Song{
    private Album $album;
    private int $note;
    private string $titre;
    private int $duree;

    public function __construct(Album $album, int $note, string $titre, int $duree){
        $this->album = $album;
        $this->note = $note;
        $this->titre = $titre;
        $this->duree = $duree;
    }

    public function getAlbum(): Album{
        return $this->album;
    }

    public function setAlbum(Album $album): void{
        $this->album = $album;
    }

    public function getNote(): int{
        return $this->note;
    }

    public function setNote(int $note): void{
        $this->note = $note;
    }

    public function getTitre(): string{
        return $this->titre;
    }

    public function setTitre(string $titre): void{
        $this->titre = $titre;
    }

    public function getDuree(): int{
        return $this->duree;
    }

    public function setDuree(int $duree): void{
        $this->duree = $duree;
    }
}