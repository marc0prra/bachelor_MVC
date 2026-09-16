<?php

require_once("Media.php");

/**
 * Class Book
 * Média de type livre, caractérisé par son nombre de pages.
 */
class Book extends Media {
    /** @var int Nombre de pages du livre. */
    private int $pageNumber;

    /**
     * @param string $title Titre du livre.
     * @param string $author Auteur du livre.
     * @param bool $disponible Disponibilité initiale.
     * @param int $pageNumber Nombre de pages.
     * @param int|null $id Identifiant en base, ou null pour un livre pas encore persisté.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     */
    public function __construct(string $title, string $author, bool $disponible, int $pageNumber, ?int $id = null, ?string $illustration = null) {
        parent::__construct($title, $author, $disponible, $id, $illustration);
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return string Le slug du type de média.
     */
    public function getType(): string {
        return 'book';
    }

    /**
     * @return int Le nombre de pages du livre.
     */
    public function getPageNumber(): int {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber Le nouveau nombre de pages.
     */
    public function setPageNumber(int $pageNumber): void {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @param string $title Titre du livre.
     * @param string $author Auteur du livre.
     * @param bool $disponible Disponibilité initiale.
     * @param array $data Données brutes du formulaire, doit contenir 'pageNumber'.
     * @return static|string Le livre créé, ou un message d'erreur si les données sont invalides.
     */
    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $pageNumber = (int) ($data['pageNumber'] ?? 0);
        if ($pageNumber <= 0) {
            return 'Le nombre de pages doit être supérieur à 0.';
        }

        $illustration = $data['illustration'] ?? null;
        return self::create($title, $author, $disponible, $pageNumber, $illustration);
    }

    /**
     * @param array $data Données brutes du formulaire, doit contenir 'pageNumber'.
     * @return string|null Un message d'erreur, ou null si les données sont valides.
     */
    public function applyFormData(array $data): ?string {
        $pageNumber = (int) ($data['pageNumber'] ?? 0);
        if ($pageNumber <= 0) {
            return 'Le nombre de pages doit être supérieur à 0.';
        }

        $this->setPageNumber($pageNumber);
        $this->applyIllustrationFormData($data);
        return null;
    }

    /**
     * Crée un nouveau livre en base et retourne l'instance correspondante.
     * @param string $title Titre du livre.
     * @param string $author Auteur du livre.
     * @param bool $disponible Disponibilité initiale.
     * @param int $pageNumber Nombre de pages.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     * @return Book Le livre créé.
     */
    public static function create(string $title, string $author, bool $disponible, int $pageNumber, ?string $illustration = null): Book {
        $id = self::insertBase($title, $author, $disponible, $illustration);

        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO Book (id, pageNumber) VALUES (:id, :pageNumber)');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':pageNumber', $pageNumber, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }

        return new Book($title, $author, $disponible, $pageNumber, $id, $illustration);
    }

    /**
     * Persiste les champs communs et le nombre de pages du livre courant.
     * @return bool True si la mise à jour a réussi.
     */
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
