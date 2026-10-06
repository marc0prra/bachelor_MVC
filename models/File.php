<?php

require_once("includes/db_connect.php");

/**
 * Class File
 * Représente un fichier enregistré dans la table File.
 */
class File {
    /** @var int Identifiant en base du fichier. */
    private int $id;

    /** @var string Nom du fichier tel qu'envoyé par l'utilisateur. */
    private string $name;

    /** @var string Chemin du fichier stocké, relatif à la racine du projet. */
    private string $path;

    /** @var string Type MIME du fichier. */
    private string $nameType;

    /** @var int Taille du fichier, en octets. */
    private int $size;

    /** @var string Date d'upload (format Y-m-d H:i:s). */
    private string $createdAt;

    /**
     * @param int $id Identifiant en base.
     * @param string $name Nom d'origine du fichier.
     * @param string $path Chemin du fichier stocké, relatif à la racine du projet.
     * @param string $nameType Type MIME du fichier.
     * @param int $size Taille en octets.
     * @param string $createdAt Date d'upload (format Y-m-d H:i:s).
     */
    public function __construct(int $id, string $name, string $path, string $nameType, int $size, string $createdAt) {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->nameType = $nameType;
        $this->size = $size;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int L'identifiant du fichier.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string Le nom d'origine du fichier.
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string Le chemin du fichier, relatif à la racine du projet (utilisable dans un src="").
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * @return string Le type MIME du fichier.
     */
    public function getNameType(): string {
        return $this->nameType;
    }

    /**
     * @return int La taille du fichier, en octets.
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * @return string La date d'upload.
     */
    public function getCreatedAt(): string {
        return $this->createdAt;
    }
}
