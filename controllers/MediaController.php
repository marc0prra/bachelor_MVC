<?php

require_once("models/Media.php");
require_once("models/Book.php");
require_once("models/Movie.php");
require_once("models/Album.php");

class MediaController {

    private const TYPES = ['book', 'movie', 'album'];
    private const SORTABLE = ['title', 'author', 'disponible'];

    static function library(?string $sortBy = null, string $direction = 'asc') {
        if ($sortBy !== null && !in_array($sortBy, self::SORTABLE, true)) {
            $sortBy = null;
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $medias = Media::getAll($sortBy, $direction);
        $message = self::consumeFlash();

        require_once('views/library.php');
    }

    static function add(string $type) {
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
                /** @var class-string<Media> $class */
                $class = ucfirst($type);
                $result = $class::fromFormData($title, $author, $disponible, $_POST);

                if (is_string($result)) {
                    $error = $result;
                } else {
                    self::redirectToLibrary('Média ajouté avec succès.');
                }
            }
        }

        require_once('views/media/add.php');
    }

    static function update(int $id) {
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
                $media->setTitle($title);
                $media->setAuthor($author);

                $error = $media->applyFormData($_POST);

                if ($error === null) {
                    $media->update();
                    self::redirectToLibrary('Média modifié avec succès.');
                }
            }
        }

        require_once('views/media/edit.php');
    }

    static function delete(int $id) {
        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        }

        Media::delete($id);
        self::redirectToLibrary('Média supprimé avec succès.');
    }

    static function borrow(int $id) {
        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        } elseif (!$media->borrow()) {
            self::redirectToLibrary('"' . $media->getTitle() . '" n\'est pas disponible.');
        } else {
            self::redirectToLibrary('Vous avez emprunté "' . $media->getTitle() . '".');
        }
    }

    static function giveBack(int $id) {
        $media = Media::find($id);

        if (!$media) {
            self::redirectToLibrary('Média introuvable.');
        } elseif (!$media->giveBack()) {
            self::redirectToLibrary('"' . $media->getTitle() . '" n\'a pas été emprunté.');
        } else {
            self::redirectToLibrary('Vous avez rendu "' . $media->getTitle() . '".');
        }
    }

    private static function redirectToLibrary(string $message): void {
        $_SESSION['flash'] = $message;
        header('Location: index.php?action=Media/library');
        exit();
    }

    private static function consumeFlash(): ?string {
        if (isset($_SESSION['flash'])) {
            $message = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $message;
        }
        return null;
    }
}
