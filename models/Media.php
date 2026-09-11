<?php

abstract class Media{
    protected string $titre;
    protected string $auteur;
    protected bool $disponible;

    public function __construct(string $titre, string $auteur, bool $disponible){
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->disponible = $disponible;
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

    public function getTitre(): string {
        return $this->titre;
    }

    public function isDisponible(): bool {
        return $this->disponible;
    }

    public function getAuteur(): string{
        return $this->auteur;
    }
}
