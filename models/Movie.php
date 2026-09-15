<?php

require_once("Media.php");

/**
 * Class Movie
 * Média de type film, caractérisé par sa durée et son genre.
 */
class Movie extends Media {
    public const GENDERS = ['Action', 'Comedie', 'Drame', 'Autre'];

    private float $duration;
    private string $gender;

    public function __construct(string $title, string $author, bool $disponible, float $duration, string $gender, ?int $id = null) {
        parent::__construct($title, $author, $disponible, $id);
        $this->duration = $duration;
        $this->gender = $gender;
    }

    public function getType(): string {
        return 'movie';
    }

    public function getDuration(): float {
        return $this->duration;
    }

    public function setDuration(float $duration): void {
        $this->duration = $duration;
    }

    public function getGender(): string {
        return $this->gender;
    }

    public function setGender(string $gender): void {
        $this->gender = $gender;
    }

    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $duration = (float) ($data['duration'] ?? 0);
        $gender = $data['gender'] ?? '';
        if ($duration <= 0 || !in_array($gender, self::GENDERS, true)) {
            return 'Durée ou genre invalide.';
        }

        return self::create($title, $author, $disponible, $duration, $gender);
    }

    public function applyFormData(array $data): ?string {
        $duration = (float) ($data['duration'] ?? 0);
        $gender = $data['gender'] ?? '';
        if ($duration <= 0 || !in_array($gender, self::GENDERS, true)) {
            return 'Durée ou genre invalide.';
        }

        $this->setDuration($duration);
        $this->setGender($gender);
        return null;
    }

    public static function create(string $title, string $author, bool $disponible, float $duration, string $gender): Movie {
        $id = self::insertBase($title, $author, $disponible);

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

        return new Movie($title, $author, $disponible, $duration, $gender, $id);
    }

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
