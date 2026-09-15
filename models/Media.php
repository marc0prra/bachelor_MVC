<?php

require_once("includes/db_connect.php");

/**
 * Class Media
 * Représente un média générique de la médiathèque (livre, film ou album).
 */
abstract class Media {
    protected int $id;
    protected string $title;
    protected string $author;
    protected bool $disponible;

    /**
     * Colonnes autorisées pour le tri, mappées vers les colonnes SQL réelles.
     */
    private const SORTABLE_COLUMNS = [
        'title' => 'm.titre',
        'author' => 'm.auteur',
        'disponible' => 'm.disponible',
    ];

    public function __construct(string $title, string $author, bool $disponible, ?int $id = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->title = $title;
        $this->author = $author;
        $this->disponible = $disponible;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function isDisponible(): bool {
        return $this->disponible;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function setAuthor(string $author): void {
        $this->author = $author;
    }

    /**
     * Slug du type de média (book, movie, album), utilisé pour le routage et l'affichage.
     */
    abstract public function getType(): string;

    /**
     * Crée un nouveau média à partir des données d'un formulaire ($_POST).
     * @return static|string L'objet créé, ou un message d'erreur si les données sont invalides.
     */
    abstract public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string;

    /**
     * Applique les champs spécifiques du sous-type depuis un formulaire ($_POST) à l'instance courante.
     * Ne persiste rien en base, seulement ->update() le fait.
     * @return string|null Un message d'erreur, ou null si les données sont valides.
     */
    abstract public function applyFormData(array $data): ?string;

    /**
     * Persiste les champs communs et spécifiques du sous-type en base.
     */
    abstract public function update(): bool;

    // emprunter un media
    public function borrow(): bool {
        if (!$this->disponible) {
            return false;
        }
        $this->disponible = false;
        return $this->updateBase();
    }

    // rendre un media
    public function giveBack(): bool {
        if ($this->disponible) {
            return false;
        }
        $this->disponible = true;
        return $this->updateBase();
    }

    /**
     * Récupère tous les médias, avec les champs spécifiques à chaque sous-type.
     * @param string|null $sortBy title|author|disponible
     * @param string $direction asc|desc
     * @return Media[]
     */
    public static function getAll(?string $sortBy = null, string $direction = 'asc'): array {
        try {
            $db = connection();
            $sql = "SELECT m.id, m.titre, m.auteur, m.disponible,
                           b.pageNumber,
                           mv.duration, mv.gender,
                           a.trackNumber, a.editor
                    FROM Media m
                    LEFT JOIN Book b ON b.id = m.id
                    LEFT JOIN Movie mv ON mv.id = m.id
                    LEFT JOIN Album a ON a.id = m.id";

            if ($sortBy !== null && isset(self::SORTABLE_COLUMNS[$sortBy])) {
                $direction = strtolower($direction) === 'desc' ? 'DESC' : 'ASC';
                $sql .= " ORDER BY " . self::SORTABLE_COLUMNS[$sortBy] . " " . $direction;
            }

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([self::class, 'hydrate'], $rows);
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    /**
     * Récupère un média par son id, avec son sous-type déjà résolu.
     */
    public static function find(int $id): ?Media {
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT m.id, m.titre, m.auteur, m.disponible,
                                          b.pageNumber,
                                          mv.duration, mv.gender,
                                          a.trackNumber, a.editor
                                   FROM Media m
                                   LEFT JOIN Book b ON b.id = m.id
                                   LEFT JOIN Movie mv ON mv.id = m.id
                                   LEFT JOIN Album a ON a.id = m.id
                                   WHERE m.id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? self::hydrate($row) : null;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    /**
     * Supprime un média (la suppression se propage à Book/Movie/Album via ON DELETE CASCADE).
     */
    public static function delete(int $id): bool {
        try {
            $db = connection();
            $stmt = $db->prepare('DELETE FROM Media WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    /**
     * Construit l'objet Book/Movie/Album correspondant à partir d'une ligne jointe.
     */
    private static function hydrate(array $row): Media {
        $title = $row['titre'];
        $author = $row['auteur'];
        $disponible = (bool) $row['disponible'];
        $id = (int) $row['id'];

        if ($row['pageNumber'] !== null) {
            return new Book($title, $author, $disponible, (int) $row['pageNumber'], $id);
        }

        if ($row['duration'] !== null) {
            return new Movie($title, $author, $disponible, (float) $row['duration'], $row['gender'], $id);
        }

        if ($row['trackNumber'] !== null) {
            return new Album($title, $author, $disponible, (int) $row['trackNumber'], $row['editor'], $id);
        }

        throw new RuntimeException("Media #$id n'a pas de sous-type correspondant (Book/Movie/Album).");
    }

    /**
     * Insère la ligne commune dans Media et retourne l'id généré.
     */
    protected static function insertBase(string $title, string $author, bool $disponible): int {
        $db = connection();
        $stmt = $db->prepare('INSERT INTO Media (titre, auteur, disponible) VALUES (:titre, :auteur, :disponible)');
        $stmt->bindValue(':titre', $title, PDO::PARAM_STR);
        $stmt->bindValue(':auteur', $author, PDO::PARAM_STR);
        $stmt->bindValue(':disponible', $disponible, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $db->lastInsertId();
    }

    /**
     * Met à jour les champs communs (titre, auteur, disponible) du média courant.
     */
    protected function updateBase(): bool {
        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE Media
                SET titre = :titre, auteur = :auteur, disponible = :disponible
                WHERE id = :id');
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':titre', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':auteur', $this->author, PDO::PARAM_STR);
            $stmt->bindValue(':disponible', $this->disponible, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
