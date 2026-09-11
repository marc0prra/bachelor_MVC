<?php 

require_once("Media.php");

class Album extends Media {
    public int $trackNumber;
    public string $editor;
    public function __construct(string $titre, string $auteur, bool $disponible, int $trackNumber, string $editor) {
        $this->trackNumber = $trackNumber;
        $this->editor = $editor;
    }

    public function getTrackNumber(): int {
        return $this->trackNumber;
    }

    public function setTrackNumber(int $trackNumber): void{
        $this->trackNumber = $trackNumber;
    } 

    public function getEditor(): string{
        return $this->editor;
    }

    public function setEditor(string $editor): void{
        $this->editor = $editor;
    }
}
