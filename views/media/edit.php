<?php
/** @var Media $media */
/** @var string|null $error */

$typeLabels = [
    'book' => 'un livre',
    'movie' => 'un film',
    'album' => 'un album',
];
$type = $media->getType();
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></title>
</head>
<body>
    <h1>Modifier <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="index.php?action=Media/update/<?= $media->getId() ?>" method="post">
        <div>
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($isPost ? ($_POST['title'] ?? '') : $media->getTitle()) ?>" required>
        </div>
        <div>
            <label for="author">Auteur :</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($isPost ? ($_POST['author'] ?? '') : $media->getAuthor()) ?>" required>
        </div>

        <?php if ($media instanceof Book): ?>
            <div>
                <label for="pageNumber">Nombre de pages :</label>
                <input type="number" id="pageNumber" name="pageNumber" min="1"
                       value="<?= htmlspecialchars($isPost ? ($_POST['pageNumber'] ?? '') : (string) $media->getPageNumber()) ?>" required>
            </div>
        <?php elseif ($media instanceof Movie): ?>
            <div>
                <label for="duration">Durée (minutes) :</label>
                <input type="number" id="duration" name="duration" min="1" step="0.1"
                       value="<?= htmlspecialchars($isPost ? ($_POST['duration'] ?? '') : (string) $media->getDuration()) ?>" required>
            </div>
            <div>
                <label for="gender">Genre :</label>
                <?php $currentGender = $isPost ? ($_POST['gender'] ?? '') : $media->getGender(); ?>
                <select id="gender" name="gender" required>
                    <?php foreach (Movie::GENDERS as $gender): ?>
                        <option value="<?= htmlspecialchars($gender) ?>" <?= $currentGender === $gender ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gender) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php elseif ($media instanceof Album): ?>
            <div>
                <label for="trackNumber">Nombre de pistes :</label>
                <input type="number" id="trackNumber" name="trackNumber" min="1"
                       value="<?= htmlspecialchars($isPost ? ($_POST['trackNumber'] ?? '') : (string) $media->getTrackNumber()) ?>" required>
            </div>
            <div>
                <label for="editor">Éditeur :</label>
                <input type="text" id="editor" name="editor"
                       value="<?= htmlspecialchars($isPost ? ($_POST['editor'] ?? '') : $media->getEditor()) ?>" required>
            </div>
        <?php endif; ?>

        <input type="submit" value="Enregistrer">
    </form>

    <p>
        Disponibilité :
        <?php if ($media->isDisponible()): ?>
            Disponible — <a href="index.php?action=Media/borrow/<?= $media->getId() ?>">Emprunter</a>
        <?php else: ?>
            Emprunté — <a href="index.php?action=Media/giveBack/<?= $media->getId() ?>">Rendre</a>
        <?php endif; ?>
    </p>

    <p><a href="index.php?action=Media/library">Retour à la médiathèque</a></p>
</body>
</html>
