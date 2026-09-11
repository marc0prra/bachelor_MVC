<?php

include("includes/db_connect.php");

abstract class Media{
    protected int $id;
    protected string $title;
    protected string $author;
    protected bool $disponible;

    public function __construct(string $title, string $author, bool $disponible){
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

    public function setTtitle(string $title): void {
        $this->title = $title;
    }

    public function isDisponible(): bool {
        return $this->disponible;
    }

    public function getauthor(): string{
        return $this->author;
    }

    public function setauthor(string $author): void {
        $this->author = $author;
    }

    public function borrow(): void {
        if ($this->disponible) {
            $this->disponible = false;
            echo "Vous avez emprunté " . $this->title;
        } else {
            echo $this->title . " n'est pas disponible";
        }
    }
    

    public function giveBack(): void {
        if (!$this->disponible) {
            $this->disponible = true;
            echo "Vous avez rendu  " . $this->title;
        } else {
            echo $this->title . " n'a pas été emprunté, impossible de le rendre";
        }
    }

    public static function getMedias() {
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT * FROM Media");
            $stmt->execute();
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $books;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    public function getMediaById(int $id){
        try {
            $db = connection();
            $stmt = $db->prepare("SELECT * FROM Media WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $media = $stmt->fetch(PDO::FETCH_ASSOC);
            return $media;
        } catch (PDOException $e) {
            die ("Erreur lors de la requete : " . $e->getMessage());
        }
    }

    public function update(int $id, string $title, string $author, bool $disponible) {
        try {
            $db = connection();
            $stmt = $db->prepare('UPDATE media
                SET title = :title, auhtor = :author, disponible = :disponible
                WHERE id = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':author', $author, PDO::PARAM_STR);
            $stmt->bindValue(':disponible', $disponible, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }
}
