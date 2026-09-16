<?php

require_once("includes/auth.php");
require_once("models/Media.php");
require_once("models/Book.php");
require_once("models/Movie.php");
require_once("models/Album.php");

/**
 * Class MediaController
 * Gère l'affichage de la médiathèque ainsi que le CRUD des médias (livres, films, albums)
 * et de leurs illustrations.
 */
class MediaController {

    private const TYPES = ['book', 'movie', 'album'];
    private const SORTABLE = ['title', 'author', 'disponible'];

    /** Dossier public de stockage des illustrations, relatif à la racine du projet. */
    private const ILLUSTRATION_DIR = 'assets/uploads/media/';

    private const ILLUSTRATION_MAX_SIZE = 2 * 1024 * 1024;

    /** Extensions autorisées, indexées par type MIME détecté. */
    private const ILLUSTRATION_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

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

            if ($title === '' || $author === '') {
                $error = "Le titre et l'auteur sont obligatoires.";
            } else {
                [$illustration, $error] = self::handleIllustrationUpload();

                if ($error === null) {
                    $data = $_POST;
                    if ($illustration !== null) {
                        $data['illustration'] = $illustration;
                    }

                    /** @var class-string<Media> $class */
                    $class = ucfirst($type);
                    $result = $class::fromFormData($title, $author, $disponible, $data);

                    if (is_string($result)) {
                        if ($illustration !== null) {
                            self::deleteIllustrationFile($illustration);
                        }
                        $error = $result;
                    } else {
                        self::redirectToLibrary('Média ajouté avec succès.');
                    }
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

            if ($title === '' || $author === '') {
                $error = "Le titre et l'auteur sont obligatoires.";
            } else {
                [$illustration, $error] = self::handleIllustrationUpload();

                if ($error === null) {
                    $previousIllustration = $media->getIllustration();

                    $media->setTitle($title);
                    $media->setAuthor($author);

                    $data = $_POST;
                    if ($illustration !== null) {
                        $data['illustration'] = $illustration;
                    }

                    $error = $media->applyFormData($data);

                    if ($error === null) {
                        $media->update();

                        if ($illustration !== null && $previousIllustration !== null) {
                            self::deleteIllustrationFile($previousIllustration);
                        }

                        self::redirectToLibrary('Média modifié avec succès.');
                    } elseif ($illustration !== null) {
                        self::deleteIllustrationFile($illustration);
                    }
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

        Media::delete($id);

        if ($media->getIllustration() !== null) {
            self::deleteIllustrationFile($media->getIllustration());
        }

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
     * Traite l'upload optionnel d'une illustration envoyée dans $_FILES['illustration'].
     * @return array{0: string|null, 1: string|null} Nom du fichier stocké et message d'erreur.
     */
    private static function handleIllustrationUpload(): array {
        if (!isset($_FILES['illustration']) || $_FILES['illustration']['error'] === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }

        $file = $_FILES['illustration'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [null, "Erreur lors de l'envoi de l'illustration."];
        }

        if ($file['size'] > self::ILLUSTRATION_MAX_SIZE) {
            return [null, "L'illustration ne doit pas dépasser 2 Mo."];
        }

        $mimeType = mime_content_type($file['tmp_name']);

        if (!isset(self::ILLUSTRATION_MIME_TYPES[$mimeType])) {
            return [null, "L'illustration doit être une image (JPEG, PNG, WEBP ou GIF)."];
        }

        $directory = ROOT . self::ILLUSTRATION_DIR;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return [null, "Impossible de créer le dossier de stockage des illustrations."];
        }

        $filename = uniqid('media_', true) . '.' . self::ILLUSTRATION_MIME_TYPES[$mimeType];

        if (!move_uploaded_file($file['tmp_name'], $directory . $filename)) {
            return [null, "Impossible d'enregistrer l'illustration."];
        }

        return [$filename, null];
    }

    /**
     * Supprime un fichier d'illustration du dossier de stockage, s'il existe.
     */
    private static function deleteIllustrationFile(string $filename): void {
        $path = ROOT . self::ILLUSTRATION_DIR . $filename;
        if (is_file($path)) {
            unlink($path);
        }
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
