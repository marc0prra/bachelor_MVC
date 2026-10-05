<?php

require_once("includes/db_connect.php");

/**
 * Class File
 * Représente un fichier uploadé (illustration) rattaché à un média, enregistré dans la table Files
 * et stocké physiquement dans le dossier uploads/.
 */
class File {
    /** Dossier public de stockage des fichiers, relatif à la racine du projet. */
    public const UPLOAD_DIR = 'uploads/';

    /** Taille maximale d'un fichier, en octets (2 Mo). */
    public const MAX_SIZE = 2 * 1024 * 1024;

    /** Extensions autorisées, indexées par type MIME détecté côté serveur. */
    public const MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    /** @var int Identifiant en base du fichier. */
    private int $id;

    /** @var int Identifiant du média auquel le fichier est rattaché. */
    private int $mediaId;

    /** @var string Nom du fichier tel qu'envoyé par l'utilisateur. */
    private string $originalName;

    /** @var string Nom unique sous lequel le fichier est stocké dans uploads/. */
    private string $storedName;

    /** @var int Taille du fichier, en octets. */
    private int $size;

    /** @var string Type MIME détecté côté serveur. */
    private string $mimeType;

    /** @var string Date d'upload (format Y-m-d H:i:s). */
    private string $uploadedAt;

    /** @var int|null Identifiant de l'utilisateur ayant uploadé le fichier, ou null s'il a été supprimé. */
    private ?int $uploadedBy;

    /**
     * @param int $id Identifiant en base.
     * @param int $mediaId Identifiant du média rattaché.
     * @param string $originalName Nom d'origine du fichier.
     * @param string $storedName Nom de stockage unique.
     * @param int $size Taille en octets.
     * @param string $mimeType Type MIME.
     * @param string $uploadedAt Date d'upload (format Y-m-d H:i:s).
     * @param int|null $uploadedBy Identifiant de l'utilisateur ayant uploadé le fichier.
     */
    public function __construct(int $id, int $mediaId, string $originalName, string $storedName, int $size, string $mimeType, string $uploadedAt, ?int $uploadedBy) {
        $this->id = $id;
        $this->mediaId = $mediaId;
        $this->originalName = $originalName;
        $this->storedName = $storedName;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->uploadedAt = $uploadedAt;
        $this->uploadedBy = $uploadedBy;
    }

    /**
     * @return int L'identifiant du fichier.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return int L'identifiant du média rattaché.
     */
    public function getMediaId(): int {
        return $this->mediaId;
    }

    /**
     * @return string Le nom d'origine du fichier.
     */
    public function getOriginalName(): string {
        return $this->originalName;
    }

    /**
     * @return string Le nom de stockage unique du fichier.
     */
    public function getStoredName(): string {
        return $this->storedName;
    }

    /**
     * @return int La taille du fichier, en octets.
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * @return string Le type MIME du fichier.
     */
    public function getMimeType(): string {
        return $this->mimeType;
    }

    /**
     * @return string La date d'upload.
     */
    public function getUploadedAt(): string {
        return $this->uploadedAt;
    }

    /**
     * @return int|null L'identifiant de l'utilisateur ayant uploadé le fichier.
     */
    public function getUploadedBy(): ?int {
        return $this->uploadedBy;
    }

    /**
     * @return string Le chemin public du fichier, utilisable dans un attribut src.
     */
    public function getPath(): string {
        return self::UPLOAD_DIR . $this->storedName;
    }

    /**
     * Vérifie côté serveur un fichier issu de $_FILES : erreur d'envoi, taille réelle et type MIME réel.
     * @param array $upload Entrée de $_FILES (name, tmp_name, size, error).
     * @return string|null Un message d'erreur, ou null si le fichier est valide.
     */
    public static function validateUpload(array $upload): ?string {
        $error = $upload['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return "L'illustration ne doit pas dépasser 2 Mo.";
        }

        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])) {
            return "Erreur lors de l'envoi de l'illustration.";
        }

        $size = filesize($upload['tmp_name']);
        if ($size === false || $size === 0 || $size > self::MAX_SIZE) {
            return "L'illustration doit être un fichier non vide de 2 Mo maximum.";
        }

        // Le type est détecté à partir du contenu du fichier, jamais à partir de son nom ou du type envoyé par le navigateur.
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        if (!isset(self::MIME_TYPES[$mimeType]) || getimagesize($upload['tmp_name']) === false) {
            return "L'illustration doit être une image (JPEG, PNG, WEBP ou GIF).";
        }

        return null;
    }

    /**
     * Valide puis enregistre un fichier uploadé dans uploads/ sous un nom unique, et le trace dans la table Files.
     * @param array $upload Entrée de $_FILES (name, tmp_name, size, error).
     * @param int $mediaId Identifiant du média rattaché.
     * @param int|null $uploadedBy Identifiant de l'utilisateur connecté.
     * @return File|string Le fichier enregistré, ou un message d'erreur.
     */
    public static function store(array $upload, int $mediaId, ?int $uploadedBy): File|string {
        $error = self::validateUpload($upload);
        if ($error !== null) {
            return $error;
        }

        $directory = ROOT . self::UPLOAD_DIR;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return "Impossible de créer le dossier de stockage des illustrations.";
        }

        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        $size = filesize($upload['tmp_name']);
        $originalName = mb_substr(basename($upload['name'] ?? 'illustration'), 0, 255);
        $storedName = bin2hex(random_bytes(16)) . '.' . self::MIME_TYPES[$mimeType];

        if (!move_uploaded_file($upload['tmp_name'], $directory . $storedName)) {
            return "Impossible d'enregistrer l'illustration.";
        }

        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO Files (media_id, original_name, stored_name, size, mime_type, uploaded_at, uploaded_by)
                VALUES (:media_id, :original_name, :stored_name, :size, :mime_type, NOW(), :uploaded_by)');
            $stmt->bindValue(':media_id', $mediaId, PDO::PARAM_INT);
            $stmt->bindValue(':original_name', $originalName, PDO::PARAM_STR);
            $stmt->bindValue(':stored_name', $storedName, PDO::PARAM_STR);
            $stmt->bindValue(':size', $size, PDO::PARAM_INT);
            $stmt->bindValue(':mime_type', $mimeType, PDO::PARAM_STR);
            $stmt->bindValue(':uploaded_by', $uploadedBy, $uploadedBy === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();

            return new File((int) $db->lastInsertId(), $mediaId, $originalName, $storedName, $size, $mimeType, date('Y-m-d H:i:s'), $uploadedBy);
        } catch (PDOException $e) {
            // Pas de fichier orphelin sur le disque si l'enregistrement en base échoue.
            unlink($directory . $storedName);
            return "Impossible d'enregistrer l'illustration.";
        }
    }

    /**
     * Récupère l'illustration la plus récente d'un média.
     * @param int $mediaId Identifiant du média.
     * @return File|null Le fichier trouvé, ou null si le média n'a pas d'illustration.
     */
    public static function findByMedia(int $mediaId): ?File {
        return self::findByMediaIds([$mediaId])[$mediaId] ?? null;
    }

    /**
     * Récupère en une seule requête l'illustration la plus récente de chacun des médias demandés.
     * @param int[] $mediaIds Identifiants des médias.
     * @return array<int, File> Les fichiers, indexés par identifiant de média.
     */
    public static function findByMediaIds(array $mediaIds): array {
        if ($mediaIds === []) {
            return [];
        }

        try {
            $db = connection();
            $placeholders = implode(',', array_fill(0, count($mediaIds), '?'));
            $stmt = $db->prepare("SELECT * FROM Files WHERE media_id IN ($placeholders) ORDER BY id ASC");
            $stmt->execute(array_map('intval', array_values($mediaIds)));

            $files = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                // Tri par id croissant : le fichier le plus récent écrase les précédents.
                $files[(int) $row['media_id']] = self::hydrate($row);
            }
            return $files;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    /**
     * Supprime le fichier en base puis sur le disque.
     * @return bool True si la suppression a réussi.
     */
    public function delete(): bool {
        try {
            $db = connection();
            $stmt = $db->prepare('DELETE FROM Files WHERE id = :id');
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }

        $path = ROOT . $this->getPath();
        return !is_file($path) || unlink($path);
    }

    /**
     * @param array $row Ligne issue de la table Files.
     * @return File L'instance correspondante.
     */
    private static function hydrate(array $row): File {
        return new File(
            (int) $row['id'],
            (int) $row['media_id'],
            $row['original_name'],
            $row['stored_name'],
            (int) $row['size'],
            $row['mime_type'],
            $row['uploaded_at'],
            $row['uploaded_by'] !== null ? (int) $row['uploaded_by'] : null
        );
    }
}
