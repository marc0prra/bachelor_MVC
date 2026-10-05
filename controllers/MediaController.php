<?php

require_once("includes/auth.php");
require_once("models/Media.php");
require_once("models/Book.php");
require_once("models/Movie.php");
require_once("models/Album.php");
require_once("models/File.php");

/**
 * Class MediaController
 * Gère l'affichage de la médiathèque ainsi que le CRUD des médias (livres, films, albums)
 * et de leurs illustrations.
 */
class MediaController {

    private const TYPES = ['book', 'movie', 'album'];
    private const SORTABLE = ['title', 'author', 'disponible'];

    /**
     * Affiche la médiathèque, triée et filtrée par recherche approximative si demandé.
     * @param string|null $sortBy Colonne de tri : title|author|disponible.
     * @param string $direction Sens du tri : asc|desc.
     */
    static function library(?string $sortBy = null, string $direction = 'asc') {
        if ($sortBy !== null && !in_array($sortBy, self::SORTABLE, true)) {
            $sortBy = null;
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $search = trim($_GET['q'] ?? '');

        $medias = Media::getAll($sortBy, $direction);
        if ($search !== '') {
            $medias = Media::search($medias, $search);
        }

        $message = consumeFlash();

        require_once('views/library.php');
    }

    /**
     * Affiche le tableau de bord (statistiques et liste des médias), réservé aux utilisateurs authentifiés.
     */
    static function dashboard() {
        requireAuth();

        $medias = Media::getAll('title');

        $total = count($medias);
        $available = count(array_filter($medias, fn(Media $media) => $media->isDisponible()));

        $stats = [
            'total' => $total,
            'available' => $available,
            'borrowed' => $total - $available,
        ];

        require_once('views/dashboard.php');
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un média, réservé aux utilisateurs authentifiés.
     * @param string $type Type de média à ajouter : book|movie|album.
     */
    static function add(string $type) {
        requireAuth();

        if (!in_array($type, self::TYPES, true)) {
            self::redirectToLibrary("Type de média inconnu.");
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            $disponible = isset($_POST['disponible']);
            $upload = self::getIllustrationUpload();

            if ($title === '' || $author === '') {
                $error = "Le titre et l'auteur sont obligatoires.";
            } elseif ($upload !== null && ($error = File::validateUpload($upload)) !== null) {
                // Fichier refusé avant toute écriture en base : $error est affiché dans le formulaire.
            } else {
                /** @var class-string<Media> $class */
                $class = ucfirst($type);
                $result = $class::fromFormData($title, $author, $disponible, $_POST);

                if (is_string($result)) {
                    $error = $result;
                } else {
                    if ($upload !== null) {
                        $file = File::store($upload, $result->getId(), $_SESSION['user_id']);
                        if (is_string($file)) {
                            self::redirectToLibrary("Média ajouté, mais l'illustration n'a pas pu être enregistrée : " . $file);
                        }
                    }
                    self::redirectToLibrary('Média ajouté avec succès.');
                }
            }
        }

        require_once('views/media/add.php');
    }

    /**
     * Affiche et traite le formulaire de modification d'un média, réservé aux utilisateurs authentifiés.
     * @param int $id Identifiant du média à modifier.
     */
    static function update(int $id) {
        requireAuth();

        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            $upload = self::getIllustrationUpload();

            if ($title === '' || $author === '') {
                $error = "Le titre et l'auteur sont obligatoires.";
            } elseif ($upload !== null && ($error = File::validateUpload($upload)) !== null) {
                // Fichier refusé avant toute modification : $error est affiché dans le formulaire.
            } else {
                $media->setTitle($title);
                $media->setAuthor($author);

                $error = $media->applyFormData($_POST);

                if ($error === null) {
                    $media->update();

                    if ($upload !== null) {
                        $file = File::store($upload, $media->getId(), $_SESSION['user_id']);
                        if (is_string($file)) {
                            self::redirectToLibrary("Média modifié, mais l'illustration n'a pas pu être enregistrée : " . $file);
                        }
                        // La nouvelle illustration est enregistrée : l'ancienne peut être supprimée.
                        $media->getIllustration()?->delete();
                    }

                    self::redirectToLibrary('Média modifié avec succès.');
                }
            }
        }

        require_once('views/media/edit.php');
    }

    /**
     * Supprime un média et son illustration, réservé aux utilisateurs authentifiés.
     * @param int $id Identifiant du média à supprimer.
     */
    static function delete(int $id) {
        requireAuth();

        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        }

        $media->getIllustration()?->delete();
        Media::delete($id);

        self::redirectToLibrary('Média supprimé avec succès.');
    }

    /**
     * Emprunte un média, réservé aux utilisateurs authentifiés.
     * @param int $id Identifiant du média à emprunter.
     */
    static function borrow(int $id) {
        requireAuth();

        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        } elseif (!$media->borrow()) {
            self::redirectToLibrary('"' . $media->getTitle() . '" n\'est pas disponible.');
        } else {
            self::redirectToLibrary('Vous avez emprunté "' . $media->getTitle() . '".');
        }
    }

    /**
     * Rend un média emprunté, réservé aux utilisateurs authentifiés.
     * @param int $id Identifiant du média à rendre.
     */
    static function giveBack(int $id) {
        requireAuth();

        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        } elseif (!$media->giveBack()) {
            self::redirectToLibrary('"' . $media->getTitle() . '" n\'a pas été emprunté.');
        } else {
            self::redirectToLibrary('Vous avez rendu "' . $media->getTitle() . '".');
        }
    }

    /**
     * Récupère l'illustration envoyée dans $_FILES['illustration'], si l'utilisateur en a choisi une.
     * @return array|null L'entrée de $_FILES, ou null si aucun fichier n'a été envoyé.
     */
    private static function getIllustrationUpload(): ?array {
        if (!isset($_FILES['illustration']) || $_FILES['illustration']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        return $_FILES['illustration'];
    }

    /**
     * Enregistre un message flash puis redirige vers la médiathèque.
     * @param string $message Message à afficher sur la page suivante.
     */
    private static function redirectToLibrary(string $message): void {
        setFlash($message);
        header('Location: index.php?action=Media/library');
        exit();
    }
}
