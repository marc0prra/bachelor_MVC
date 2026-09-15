<?php

/**
 * Stocke un message à afficher lors du prochain affichage d'une vue.
 */
function setFlash(string $message): void {
    $_SESSION['flash'] = $message;
}

/**
 * Récupère puis efface le message flash en attente, s'il y en a un.
 */
function consumeFlash(): ?string {
    if (isset($_SESSION['flash'])) {
        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $message;
    }
    return null;
}
