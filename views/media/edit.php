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

$title = 'Modifier ' . ($typeLabels[$type] ?? $type);
require_once('views/partials/header.php');
?>

<div class="auth-wrap">
    <div class="card form-card narrow" style="width:100%;">
        <h1>Modifier <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></h1>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?action=Media/update/<?= $media->getId() ?>" method="post">
            <div class="form-field">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($isPost ? ($_POST['title'] ?? '') : $media->getTitle()) ?>" required>
            </div>
            <div class="form-field">
                <label for="author">Auteur</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($isPost ? ($_POST['author'] ?? '') : $media->getAuthor()) ?>" required>
            </div>

            <?php if ($media instanceof Book): ?>
                <div class="form-field">
                    <label for="pageNumber">Nombre de pages</label>
                    <input type="number" id="pageNumber" name="pageNumber" min="1"
                           value="<?= htmlspecialchars($isPost ? ($_POST['pageNumber'] ?? '') : (string) $media->getPageNumber()) ?>" required>
                </div>
            <?php elseif ($media instanceof Movie): ?>
                <div class="form-field">
                    <label for="duration">Durée (minutes)</label>
                    <input type="number" id="duration" name="duration" min="1" step="0.1"
                           value="<?= htmlspecialchars($isPost ? ($_POST['duration'] ?? '') : (string) $media->getDuration()) ?>" required>
                </div>
                <div class="form-field">
                    <label for="gender">Genre</label>
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
                <div class="form-field">
                    <label for="trackNumber">Nombre de pistes</label>
                    <input type="number" id="trackNumber" name="trackNumber" min="1"
                           value="<?= htmlspecialchars($isPost ? ($_POST['trackNumber'] ?? '') : (string) $media->getTrackNumber()) ?>" required>
                </div>
                <div class="form-field">
                    <label for="editor">Éditeur</label>
                    <input type="text" id="editor" name="editor"
                           value="<?= htmlspecialchars($isPost ? ($_POST['editor'] ?? '') : $media->getEditor()) ?>" required>
                </div>
            <?php endif; ?>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
            </div>
        </form>

        <p class="form-foot-link">
            <?php if ($media->isDisponible()): ?>
                <span class="badge badge-success">Disponible</span> —
                <a href="index.php?action=Media/borrow/<?= $media->getId() ?>">Emprunter</a>
            <?php else: ?>
                <span class="badge badge-danger">Emprunté</span> —
                <a href="index.php?action=Media/giveBack/<?= $media->getId() ?>">Rendre</a>
            <?php endif; ?>
        </p>

        <p class="form-foot-link"><a href="index.php?action=Media/library">← Retour à la médiathèque</a></p>
    </div>
</div>

<?php require_once('views/partials/footer.php'); ?>
