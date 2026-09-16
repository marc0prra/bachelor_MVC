<?php

require_once("Media.php");

/**
 * Class Movie
 * Média de type film, caractérisé par sa durée et son genre.
 */
class Movie extends Media {
    /** @var string[] Genres autorisés pour un film. */
    public const GENDERS = ['Action', 'Comedie', 'Drame', 'Autre'];

    /** @var float Durée du film, en minutes. */
    private float $duration;

    /** @var string Genre du film, parmi Movie::GENDERS. */
    private string $gender;

    /**
     * @param string $title Titre du film.
     * @param string $author Auteur/réalisateur du film.
     * @param bool $disponible Disponibilité initiale.
     * @param float $duration Durée en minutes.
     * @param string $gender Genre du film, parmi Movie::GENDERS.
     * @param int|null $id Identifiant en base, ou null pour un film pas encore persisté.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     */
    public function __construct(string $title, string $author, bool $disponible, float $duration, string $gender, ?int $id = null, ?string $illustration = null) {
        parent::__construct($title, $author, $disponible, $id, $illustration);
        $this->duration = $duration;
        $this->gender = $gender;
    }

    /**
     * @return string Le slug du type de média.
     */
    public function getType(): string {
        return 'movie';
    }

    /**
     * @return float La durée du film, en minutes.
     */
    public function getDuration(): float {
        return $this->duration;
    }

    /**
     * @param float $duration La nouvelle durée, en minutes.
     */
    public function setDuration(float $duration): void {
        $this->duration = $duration;
    }

    /**
     * @return string Le genre du film.
     */
    public function getGender(): string {
        return $this->gender;
    }

    /**
     * @param string $gender Le nouveau genre, parmi Movie::GENDERS.
     */
    public function setGender(string $gender): void {
        $this->gender = $gender;
    }

    /**
     * @param string $title Titre du film.
     * @param string $author Auteur/réalisateur du film.
     * @param bool $disponible Disponibilité initiale.
     * @param array $data Données brutes du formulaire, doit contenir 'duration' et 'gender'.
     * @return static|string Le film créé, ou un message d'erreur si les données sont invalides.
     */
    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $duration = (float) ($data['duration'] ?? 0);
        $gender = $data['gender'] ?? '';
        if ($duration <= 0 || !in_array($gender, self::GENDERS, true)) {
            return 'Durée ou genre invalide.';
        }

        $illustration = $data['illustration'] ?? null;
        return self::create($title, $author, $disponible, $duration, $gender, $illustration);
    }

    /**
     * @param array $data Données brutes du formulaire, doit contenir 'duration' et 'gender'.
     * @return string|null Un message d'erreur, ou null si les données sont valides.
     */
    public function applyFormData(array $data): ?string {
        $duration = (float) ($data['duration'] ?? 0);
        $gender = $data['gender'] ?? '';
        if ($duration <= 0 || !in_array($gender, self::GENDERS, true)) {
            return 'Durée ou genre invalide.';
        }

        $this->setDuration($duration);
        $this->setGender($gender);
        $this->applyIllustrationFormData($data);
        return null;
    }

    /**
     * Crée un nouveau film en base et retourne l'instance correspondante.
     * @param string $title Titre du film.
     * @param string $author Auteur/réalisateur du film.
     * @param bool $disponible Disponibilité initiale.
     * @param float $duration Durée en minutes.
     * @param string $gender Genre du film, parmi Movie::GENDERS.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     * @return Movie Le film créé.
     */
    public static function create(string $title, string $author, bool $disponible, float $duration, string $gender, ?string $illustration = null): Movie {
        $id = self::insertBase($title, $author, $disponible, $illustration);

        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO Movie (id, duration, gender) VALUES (:id, :duration, :gender)');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':duration', $duration);
            $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }

        return new Movie($title, $author, $disponible, $duration, $gender, $id, $illustration);
    }

    /**
     * Persiste les champs communs, la durée et le genre du film courant.
     * @return bool True si la mise à jour a réussi.
     */
    public function update(): bool {
        $this->updateBase();

        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE Movie SET duration = :duration, gender = :gender WHERE id = :id');
            $stmt->bindValue(':duration', $this->duration);
            $stmt->bindValue(':gender', $this->gender, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
