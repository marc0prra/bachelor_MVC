<?php

require_once("includes/db_connect.php");

/**
 * Class Media
 * Représente un média générique de la médiathèque (livre, film ou album).
 */
abstract class Media {
    /** @var int Identifiant en base du média. */
    protected int $id;

    /** @var string Titre du média. */
    protected string $title;

    /** @var string Auteur du média. */
    protected string $author;

    /** @var bool Indique si le média est disponible à l'emprunt. */
    protected bool $disponible;

    /** @var string|null Nom du fichier d'illustration stocké dans assets/uploads/media/, ou null si absent. */
    protected ?string $illustration;

    /**
     * Colonnes autorisées pour le tri, mappées vers les colonnes SQL réelles.
     * @var array<string, string>
     */
    private const SORTABLE_COLUMNS = [
        'title' => 'm.titre',
        'author' => 'm.auteur',
        'disponible' => 'm.disponible',
    ];

    /**
     * @param string $title Titre du média.
     * @param string $author Auteur du média.
     * @param bool $disponible Disponibilité initiale du média.
     * @param int|null $id Identifiant en base, ou null pour un média pas encore persisté.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     */
    public function __construct(string $title, string $author, bool $disponible, ?int $id = null, ?string $illustration = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->title = $title;
        $this->author = $author;
        $this->disponible = $disponible;
        $this->illustration = $illustration;
    }

    /**
     * @return int L'identifiant du média.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id Le nouvel identifiant du média.
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @return string Le titre du média.
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title Le nouveau titre du média.
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    /**
     * @return bool True si le média est disponible à l'emprunt.
     */
    public function isDisponible(): bool {
        return $this->disponible;
    }

    /**
     * @return string L'auteur du média.
     */
    public function getAuthor(): string {
        return $this->author;
    }

    /**
     * @param string $author Le nouvel auteur du média.
     */
    public function setAuthor(string $author): void {
        $this->author = $author;
    }

    /**
     * @return string|null Le nom du fichier d'illustration, ou null si absent.
     */
    public function getIllustration(): ?string {
        return $this->illustration;
    }

    /**
     * @param string|null $illustration Le nouveau nom de fichier d'illustration, ou null pour le retirer.
     */
    public function setIllustration(?string $illustration): void {
        $this->illustration = $illustration;
    }

    /**
     * Applique l'illustration transmise dans les données d'un formulaire, si une nouvelle a été fournie.
     * Ne fait rien si la clé est absente, afin de conserver l'illustration existante.
     * @param array $data Données du formulaire ($_POST), éventuellement complétées par le contrôleur avec la clé 'illustration'.
     */
    protected function applyIllustrationFormData(array $data): void {
        if (array_key_exists('illustration', $data)) {
            $this->setIllustration($data['illustration']);
        }
    }

    /**
     * Slug du type de média (book, movie, album), utilisé pour le routage et l'affichage.
     * @return string Le slug du type de média.
     */
    abstract public function getType(): string;

    /**
     * Crée un nouveau média à partir des données d'un formulaire ($_POST).
     * @param string $title Titre du média.
     * @param string $author Auteur du média.
     * @param bool $disponible Disponibilité initiale.
     * @param array $data Données brutes du formulaire.
     * @return static|string L'objet créé, ou un message d'erreur si les données sont invalides.
     */
    abstract public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string;

    /**
     * Applique les champs spécifiques du sous-type depuis un formulaire ($_POST) à l'instance courante.
     * Ne persiste rien en base, seulement ->update() le fait.
     * @param array $data Données brutes du formulaire.
     * @return string|null Un message d'erreur, ou null si les données sont valides.
     */
    abstract public function applyFormData(array $data): ?string;

    /**
     * Persiste les champs communs et spécifiques du sous-type en base.
     * @return bool True si la mise à jour a réussi.
     */
    abstract public function update(): bool;

    /**
     * Emprunte le média s'il est disponible.
     * @return bool True si l'emprunt a réussi, false si le média n'était pas disponible.
     */
    public function borrow(): bool {
        if (!$this->disponible) {
            return false;
        }
        $this->disponible = false;
        return $this->updateBase();
    }

    /**
     * Rend le média s'il était emprunté.
     * @return bool True si le retour a réussi, false si le média était déjà disponible.
     */
    public function giveBack(): bool {
        if ($this->disponible) {
            return false;
        }
        $this->disponible = true;
        return $this->updateBase();
    }

    /**
     * Récupère tous les médias, avec les champs spécifiques à chaque sous-type.
     * @param string|null $sortBy Colonne de tri : title|author|disponible.
     * @param string $direction Sens du tri : asc|desc.
     * @return Media[] La liste des médias.
     */
    public static function getAll(?string $sortBy = null, string $direction = 'asc'): array {
        try {
            $db = connection();
            $sql = "SELECT m.id, m.titre, m.auteur, m.disponible, m.illustration,
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
     * Filtre une liste de médias par recherche approximative sur le titre et l'auteur,
     * en tolérant les fautes de frappe grâce à la distance de Levenshtein.
     * @param Media[] $medias Liste de médias à filtrer.
     * @param string $query Terme recherché.
     * @return Media[] Les médias correspondant à la recherche.
     */
    public static function search(array $medias, string $query): array {
        $query = trim($query);
        if ($query === '') {
            return $medias;
        }

        return array_values(array_filter($medias, function (Media $media) use ($query) {
            return self::matchesApproximately($media->getTitle(), $query)
                || self::matchesApproximately($media->getAuthor(), $query);
        }));
    }

    /**
     * Détermine si $haystack correspond approximativement à $query : correspondance partielle
     * (sous-chaîne) ou distance de Levenshtein faible entre $query et l'un des mots de $haystack.
     * @param string $haystack Texte dans lequel chercher (titre ou auteur).
     * @param string $query Terme recherché.
     * @return bool True si $haystack correspond approximativement à $query.
     */
    private static function matchesApproximately(string $haystack, string $query): bool {
        $haystack = mb_strtolower($haystack);
        $query = mb_strtolower($query);

        if (str_contains($haystack, $query)) {
            return true;
        }

        $maxDistance = min(3, max(1, (int) floor(mb_strlen($query) / 2)));

        foreach (preg_split('/\s+/', $haystack) as $word) {
            if (levenshtein($word, $query) <= $maxDistance) {
                return true;
            }
        }

        return false;
    }

    /**
     * Récupère un média par son id, avec son sous-type déjà résolu.
     * @param int $id Identifiant du média recherché.
     * @return Media|null Le média trouvé, ou null s'il n'existe pas.
     */
    public static function find(int $id): ?Media {
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT m.id, m.titre, m.auteur, m.disponible, m.illustration,
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
     * @param int $id Identifiant du média à supprimer.
     * @return bool True si la suppression a réussi.
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
     * @param array $row Ligne issue d'une requête jointe sur Media, Book, Movie et Album.
     * @return Media L'instance du sous-type correspondant.
     */
    private static function hydrate(array $row): Media {
        $title = $row['titre'];
        $author = $row['auteur'];
        $disponible = (bool) $row['disponible'];
        $id = (int) $row['id'];
        $illustration = $row['illustration'];

        if ($row['pageNumber'] !== null) {
            return new Book($title, $author, $disponible, (int) $row['pageNumber'], $id, $illustration);
        }

        if ($row['duration'] !== null) {
            return new Movie($title, $author, $disponible, (float) $row['duration'], $row['gender'], $id, $illustration);
        }

        if ($row['trackNumber'] !== null) {
            return new Album($title, $author, $disponible, (int) $row['trackNumber'], $row['editor'], $id, $illustration);
        }

        throw new RuntimeException("Media #$id n'a pas de sous-type correspondant (Book/Movie/Album).");
    }

    /**
     * Insère la ligne commune dans Media et retourne l'id généré.
     * @param string $title Titre du média.
     * @param string $author Auteur du média.
     * @param bool $disponible Disponibilité initiale.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     * @return int L'identifiant généré par la base de données.
     */
    protected static function insertBase(string $title, string $author, bool $disponible, ?string $illustration = null): int {
        $db = connection();
        $stmt = $db->prepare('INSERT INTO Media (titre, auteur, disponible, illustration) VALUES (:titre, :auteur, :disponible, :illustration)');
        $stmt->bindValue(':titre', $title, PDO::PARAM_STR);
        $stmt->bindValue(':auteur', $author, PDO::PARAM_STR);
        $stmt->bindValue(':disponible', $disponible, PDO::PARAM_INT);
        $stmt->bindValue(':illustration', $illustration, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $db->lastInsertId();
    }

    /**
     * Met à jour les champs communs (titre, auteur, disponible, illustration) du média courant.
     * @return bool True si la mise à jour a réussi.
     */
    protected function updateBase(): bool {
        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE Media
                SET titre = :titre, auteur = :auteur, disponible = :disponible, illustration = :illustration
                WHERE id = :id');
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':titre', $this->title, PDO::PARAM_STR);
            $stmt->bindValue(':auteur', $this->author, PDO::PARAM_STR);
            $stmt->bindValue(':disponible', $this->disponible, PDO::PARAM_INT);
            $stmt->bindValue(':illustration', $this->illustration, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
