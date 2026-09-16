<?php

/**
 * Ouvre (ou réutilise) la connexion PDO à la base de données du projet.
 * @return PDO La connexion active.
 */
function connection(){
    $serveur = "localhost";
    $utilisateur = "root";
    $mdp = "ServBay.dev";
    $bdd = "bachelorMVC";

    try {
        $connexion = new PDO("mysql:host=$serveur;dbname=$bdd", $utilisateur, $mdp);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connexion;
    } catch (PDOException $e) {
        die("Echec de la connexion : " .  $e->getMessage());
    }
}
