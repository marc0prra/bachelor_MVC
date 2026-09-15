<?php
/** @var string $type */
/** @var string|null $error */

$typeLabels = [
    'book' => 'un livre',
    'movie' => 'un film',
    'album' => 'un album',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></title>
</head>
<body>
    <h1>Ajouter <?= htmlspecialchars($typeLabels[$type] ?? $type) ?></h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="index.php?action=Media/add/<?= htmlspecialchars($type) ?>" method="post">
        <div>
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </div>
        <div>
            <label for="author">Auteur :</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($_POST['author'] ?? '') ?>" required>
        </div>
        <div>
            <label for="disponible">Disponible :</label>
            <input type="checkbox" id="disponible" name="disponible" <?= isset($_POST['disponible']) || $_SERVER['REQUEST_METHOD'] !== 'POST' ? 'checked' : '' ?>>
        </div>

        <?php if ($type === 'book'): ?>
            <div>
                <label for="pageNumber">Nombre de pages :</label>
                <input type="number" id="pageNumber" name="pageNumber" min="1" value="<?= htmlspecialchars($_POST['pageNumber'] ?? '') ?>" required>
            </div>
        <?php elseif ($type === 'movie'): ?>
            <div>
                <label for="duration">Durée (minutes) :</label>
                <input type="number" id="duration" name="duration" min="1" step="0.1" value="<?= htmlspecialchars($_POST['duration'] ?? '') ?>" required>
            </div>
            <div>
                <label for="gender">Genre :</label>
                <select id="gender" name="gender" required>
                    <?php foreach (Movie::GENDERS as $gender): ?>
                        <option value="<?= htmlspecialchars($gender) ?>" <?= (($_POST['gender'] ?? '') === $gender) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gender) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php elseif ($type === 'album'): ?>
            <div>
                <label for="trackNumber">Nombre de pistes :</label>
                <input type="number" id="trackNumber" name="trackNumber" min="1" value="<?= htmlspecialchars($_POST['trackNumber'] ?? '') ?>" required>
            </div>
            <div>
                <label for="editor">Éditeur :</label>
                <input type="text" id="editor" name="editor" value="<?= htmlspecialchars($_POST['editor'] ?? '') ?>" required>
            </div>
        <?php endif; ?>

        <input type="submit" value="Ajouter">
    </form>

    <p><a href="index.php?action=Media/library">Retour à la médiathèque</a></p>
</body>
</html>
