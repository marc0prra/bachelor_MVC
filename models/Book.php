<?php

require_once("Media.php");

class Book extends Media {
    private int $pageNumber;

    public function __construct(string $titre, string $auteur, bool $disponible, int $pageNumber) {
        parent::__construct($titre, $auteur, $disponible);
        $this->pageNumber = $pageNumber;
    }

    public function getPageNumber(): int {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void {
        $this->pageNumber = $pageNumber;
    }
}
