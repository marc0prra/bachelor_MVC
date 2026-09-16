<?php

require_once("Media.php");

/**
 * Class Album
 * Média de type album, caractérisé par son nombre de pistes et son éditeur.
 */
class Album extends Media {
    /** @var int Nombre de pistes de l'album. */
    private int $trackNumber;

    /** @var string Éditeur de l'album. */
    private string $editor;

    /**
     * @param string $title Titre de l'album.
     * @param string $author Auteur/artiste de l'album.
     * @param bool $disponible Disponibilité initiale.
     * @param int $trackNumber Nombre de pistes.
     * @param string $editor Éditeur de l'album.
     * @param int|null $id Identifiant en base, ou null pour un album pas encore persisté.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     */
    public function __construct(string $title, string $author, bool $disponible, int $trackNumber, string $editor, ?int $id = null, ?string $illustration = null) {
        parent::__construct($title, $author, $disponible, $id, $illustration);
        $this->trackNumber = $trackNumber;
        $this->editor = $editor;
    }

    /**
     * @return string Le slug du type de média.
     */
    public function getType(): string {
        return 'album';
    }

    /**
     * @return int Le nombre de pistes de l'album.
     */
    public function getTrackNumber(): int {
        return $this->trackNumber;
    }

    /**
     * @param int $trackNumber Le nouveau nombre de pistes.
     */
    public function setTrackNumber(int $trackNumber): void {
        $this->trackNumber = $trackNumber;
    }

    /**
     * @return string L'éditeur de l'album.
     */
    public function getEditor(): string {
        return $this->editor;
    }

    /**
     * @param string $editor Le nouvel éditeur.
     */
    public function setEditor(string $editor): void {
        $this->editor = $editor;
    }

    /**
     * @param string $title Titre de l'album.
     * @param string $author Auteur/artiste de l'album.
     * @param bool $disponible Disponibilité initiale.
     * @param array $data Données brutes du formulaire, doit contenir 'trackNumber' et 'editor'.
     * @return static|string L'album créé, ou un message d'erreur si les données sont invalides.
     */
    public static function fromFormData(string $title, string $author, bool $disponible, array $data): static|string {
        $trackNumber = (int) ($data['trackNumber'] ?? 0);
        $editor = trim($data['editor'] ?? '');
        if ($trackNumber <= 0 || $editor === '') {
            return 'Nombre de pistes ou éditeur invalide.';
        }

        $illustration = $data['illustration'] ?? null;
        return self::create($title, $author, $disponible, $trackNumber, $editor, $illustration);
    }

    /**
     * @param array $data Données brutes du formulaire, doit contenir 'trackNumber' et 'editor'.
     * @return string|null Un message d'erreur, ou null si les données sont valides.
     */
    public function applyFormData(array $data): ?string {
        $trackNumber = (int) ($data['trackNumber'] ?? 0);
        $editor = trim($data['editor'] ?? '');
        if ($trackNumber <= 0 || $editor === '') {
            return 'Nombre de pistes ou éditeur invalide.';
        }

        $this->setTrackNumber($trackNumber);
        $this->setEditor($editor);
        $this->applyIllustrationFormData($data);
        return null;
    }

    /**
     * Crée un nouvel album en base et retourne l'instance correspondante.
     * @param string $title Titre de l'album.
     * @param string $author Auteur/artiste de l'album.
     * @param bool $disponible Disponibilité initiale.
     * @param int $trackNumber Nombre de pistes.
     * @param string $editor Éditeur de l'album.
     * @param string|null $illustration Nom du fichier d'illustration, ou null si absent.
     * @return Album L'album créé.
     */
    public static function create(string $title, string $author, bool $disponible, int $trackNumber, string $editor, ?string $illustration = null): Album {
        $id = self::insertBase($title, $author, $disponible, $illustration);

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

        return new Album($title, $author, $disponible, $trackNumber, $editor, $id, $illustration);
    }

    /**
     * Persiste les champs communs, le nombre de pistes et l'éditeur de l'album courant.
     * @return bool True si la mise à jour a réussi.
     */
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
