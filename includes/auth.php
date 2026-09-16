<?php

require_once("includes/flash.php");

/**
 * @return bool True si un utilisateur est connecté.
 */
function isAuthenticated(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Bloque l'accès à une action et redirige vers la connexion si l'utilisateur
 * n'est pas authentifié.
 */
function requireAuth(): void {
    if (!isAuthenticated()) {
        setFlash("Vous devez être connecté pour effectuer cette action.");
        header('Location: index.php?action=User/login');
        exit();
    }
}
