<?php

require_once("Album.php");

/**
 * Class Song
 * Représente une piste appartenant à un album.
 */
class Song{
    /** @var Album Album auquel appartient la piste. */
    private Album $album;

    /** @var int Note de la piste, sur 5. */
    private int $note;

    /** @var string Titre de la piste. */
    private string $titre;

    /** @var int Durée de la piste, en secondes. */
    private int $duree;

    /**
     * @param Album $album Album auquel appartient la piste.
     * @param int $note Note de la piste, sur 5.
     * @param string $titre Titre de la piste.
     * @param int $duree Durée de la piste, en secondes.
     */
    public function __construct(Album $album, int $note, string $titre, int $duree){
        $this->album = $album;
        $this->note = $note;
        $this->titre = $titre;
        $this->duree = $duree;
    }

    /**
     * @return Album L'album auquel appartient la piste.
     */
    public function getAlbum(): Album{
        return $this->album;
    }

    /**
     * @param Album $album Le nouvel album.
     */
    public function setAlbum(Album $album): void{
        $this->album = $album;
    }

    /**
     * @return int La note de la piste, sur 5.
     */
    public function getNote(): int{
        return $this->note;
    }

    /**
     * @param int $note La nouvelle note, sur 5.
     */
    public function setNote(int $note): void{
        $this->note = $note;
    }

    /**
     * @return string Le titre de la piste.
     */
    public function getTitre(): string{
        return $this->titre;
    }

    /**
     * @param string $titre Le nouveau titre.
     */
    public function setTitre(string $titre): void{
        $this->titre = $titre;
    }

    /**
     * @return int La durée de la piste, en secondes.
     */
    public function getDuree(): int{
        return $this->duree;
    }

    /**
     * @param int $duree La nouvelle durée, en secondes.
     */
    public function setDuree(int $duree): void{
        $this->duree = $duree;
    }
}
