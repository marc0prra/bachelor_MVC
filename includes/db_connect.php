<?php

function connection(){
    $serveur = "localhost";
    $utilisateur = "root";
    $mdp = "root";
    $bdd = "bachelorMVC";

    try {
        $connexion = new PDO("mysql:host=$serveur;dbname=$bdd", $utilisateur, $mdp);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connexion;
    } catch (PDOException $e) {
        die("Echec de la connexion : " .  $e->getMessage());
    }
}
