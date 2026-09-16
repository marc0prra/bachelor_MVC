<?php
/** @var string $type */
/** @var string|null $error */

$typeLabels = [
    'book' => 'un livre',
    'movie' => 'un film',
    'album' => 'un album',
];

$title = 'Ajouter ' . ($typeLabels[$type] ?? $type);
require_once('views/partials/header.php');
?>

<div class="auth-wrap">
    <div class="card form-card narrow" style="width:100%;">
        <h1>Ajouter <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></h1>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?action=Media/add/<?= htmlspecialchars($type) ?>" method="post" enctype="multipart/form-data">
            <div class="form-field">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>
            <div class="form-field">
                <label for="author">Auteur</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($_POST['author'] ?? '') ?>" required>
            </div>
            <div class="form-field">
                <label for="illustration">Illustration</label>
                <input type="file" id="illustration" name="illustration" accept="image/jpeg,image/png,image/webp,image/gif">
                <small>JPEG, PNG, WEBP ou GIF, 2 Mo maximum.</small>
            </div>
            <div class="form-field">
                <label class="form-checkbox">
                    <input type="checkbox" id="disponible" name="disponible" <?= isset($_POST['disponible']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : '' ?>>
                    Disponible dès l'ajout
                </label>
            </div>

            <?php if ($type === 'book'): ?>
                <div class="form-field">
                    <label for="pageNumber">Nombre de pages</label>
                    <input type="number" id="pageNumber" name="pageNumber" min="1" value="<?= htmlspecialchars($_POST['pageNumber'] ?? '') ?>" required>
                </div>
            <?php elseif ($type === 'movie'): ?>
                <div class="form-field">
                    <label for="duration">Durée (minutes)</label>
                    <input type="number" id="duration" name="duration" min="1" step="0.1" value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>" required>
                </div>
                <div class="form-field">
                    <label for="gender">Genre</label>
                    <select id="gender" name="gender" required>
                        <?php foreach (Movie::GENDERS as $gender): ?>
                            <option value="<?= htmlspecialchars($gender) ?>" <?= (($_POST['gender'] ?? '') === $gender) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gender) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php elseif ($type === 'album'): ?>
                <div class="form-field">
                    <label for="trackNumber">Nombre de pistes</label>
                    <input type="number" id="trackNumber" name="trackNumber" min="1" value="<?= htmlspecialchars($_POST['trackNumber'] ?? '') ?>" required>
                </div>
                <div class="form-field">
                    <label for="editor">Éditeur</label>
                    <input type="text" id="editor" name="editor" value="<?= htmlspecialchars($_POST['editor'] ?? '') ?>" required>
                </div>
            <?php endif; ?>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">Ajouter</button>
            </div>
        </form>

        <p class="form-foot-link"><a href="index.php?action=Media/library">← Retour à la médiathèque</a></p>
    </div>
</div>

<?php require_once('views/partials/footer.php'); ?>
