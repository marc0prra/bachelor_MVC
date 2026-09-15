<?php

require_once("Media.php");

/**
 * Class Book
 * Média de type livre, caractérisé par son nombre de pages.
 */
class Book extends Media {
    private int $pageNumber;

    public function __construct(string $title, string $author, bool $disponible, int $pageNumber, ?int $id = null) {
        parent::__construct($title, $author, $disponible, $id);
        $this->pageNumber = $pageNumber;
    }

    public function getType(): string {
        return 'book';
    }

    public function getPageNumber(): int {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void {
        $this->pageNumber = $pageNumber;
    }

    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $pageNumber = (int) ($data['pageNumber'] ?? 0);
        if ($pageNumber <= 0) {
            return 'Le nombre de pages doit être supérieur à 0.';
        }

        return self::create($title, $author, $disponible, $pageNumber);
    }

    public function applyFormData(array $data): ?string {
        $pageNumber = (int) ($data['pageNumber'] ?? 0);
        if ($pageNumber <= 0) {
            return 'Le nombre de pages doit être supérieur à 0.';
        }

        $this->setPageNumber($pageNumber);
        return null;
    }

    public static function create(string $title, string $author, bool $disponible, int $pageNumber): Book {
        $id = self::insertBase($title, $author, $disponible);

        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO Book (id, pageNumber) VALUES (:id, :pageNumber)');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':pageNumber', $pageNumber, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }

        return new Book($title, $author, $disponible, $pageNumber, $id);
    }

    public function update(): bool {
        $this->updateBase();

        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE Book SET pageNumber = :pageNumber WHERE id = :id');
            $stmt->bindValue(':pageNumber', $this->pageNumber, PDO::PARAM_INT);
            $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
