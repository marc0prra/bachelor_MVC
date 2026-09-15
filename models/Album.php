<?php

require_once("Media.php");

/**
 * Class Album
 * Média de type album, caractérisé par son nombre de pistes et son éditeur.
 */
class Album extends Media {
    private int $trackNumber;
    private string $editor;

    public function __construct(string $title, string $author, bool $disponible, int $trackNumber, string $editor, ?int $id = null) {
        parent::__construct($title, $author, $disponible, $id);
        $this->trackNumber = $trackNumber;
        $this->editor = $editor;
    }

    public function getType(): string {
        return 'album';
    }

    public function getTrackNumber(): int {
        return $this->trackNumber;
    }

    public function setTrackNumber(int $trackNumber): void {
        $this->trackNumber = $trackNumber;
    }

    public function getEditor(): string {
        return $this->editor;
    }

    public function setEditor(string $editor): void {
        $this->editor = $editor;
    }

    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $trackNumber = (int) ($data['trackNumber'] ?? 0);
        $editor = trim($data['editor'] ?? '');
        if ($trackNumber <= 0 || $editor === '') {
            return 'Nombre de pistes ou éditeur invalide.';
        }

        return self::create($title, $author, $disponible, $trackNumber, $editor);
    }

    public function applyFormData(array $data): ?string {
        $trackNumber = (int) ($data['trackNumber'] ?? 0);
        $editor = trim($data['editor'] ?? '');
        if ($trackNumber <= 0 || $editor === '') {
            return 'Nombre de pistes ou éditeur invalide.';
        }

        $this->setTrackNumber($trackNumber);
        $this->setEditor($editor);
        return null;
    }

    public static function create(string $title, string $author, bool $disponible, int $trackNumber, string $editor): Album {
        $id = self::insertBase($title, $author, $disponible);

        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO Album (id, trackNumber, editor) VALUES (:id, :trackNumber, :editor)');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':trackNumber', $trackNumber, PDO::PARAM_INT);
            $stmt->bindValue(':editor', $editor, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }

        return new Album($title, $author, $disponible, $trackNumber, $editor, $id);
    }

    public function update(): bool {
        $this->updateBase();

        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE Album SET trackNumber = :trackNumber, editor = :editor WHERE id = :id');
            $stmt->bindValue(':trackNumber', $this->trackNumber, PDO::PARAM_INT);
            $stmt->bindValue(':editor', $this->editor, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
