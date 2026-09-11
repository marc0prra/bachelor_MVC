<?php

require_once("Media.php");
require_once("includes/db_config.php");

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

    public static function getBooks() {
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT * FROM books ORDER BY published_year ASC");
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $books;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
