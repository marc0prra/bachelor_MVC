<?php

abstract class Media{
    protected int $id;
    protected string $titre;
    protected string $auteur;
    protected bool $disponible;

    public function __construct(string $titre, string $auteur, bool $disponible){
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->disponible = $disponible;
    }


    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function setTitre(string $titre): void {
        $this->titre = $titre;
    }

    public function isDisponible(): bool {
        return $this->disponible;
    }

    public function getAuteur(): string{
        return $this->auteur;
    }

    public function setAuteur(string $auteur): void {
        $this->auteur = $auteur;
    }

    public function borrow(): void {
        if ($this->disponible) {
            $this->disponible = false;
            echo "Vous avez emprunté " . $this->titre;
        } else {
            echo $this->titre . " n'est pas disponible";
        }
    }
    

    public function giveBack(): void {
        if (!$this->disponible) {
            $this->disponible = true;
            echo "Vous avez rendu  " . $this->titre;
        } else {
            echo $this->titre . " n'a pas été emprunté, impossible de le rendre";
        }
    }


    public function getBookById($id){
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT * FROM Book WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            return $book;
        } catch (PDOException $e) {
            die ("Erreur lors de la requete : " . $e->getMessage());
        }
    }
}
