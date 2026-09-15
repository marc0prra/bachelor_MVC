<?php
/** @var Media[] $medias */
/** @var string|null $sortBy */
/** @var string $direction */
/** @var string|null $message */

$typeLabels = [
    'book' => 'Livre',
    'movie' => 'Film',
    'album' => 'Album',
];

function sortLink(string $column, string $label, ?string $sortBy, string $direction): string {
    $nextDirection = ($sortBy === $column && $direction === 'asc') ? 'desc' : 'asc';
    $arrow = $sortBy === $column ? ($direction === 'asc' ? ' ▲' : ' ▼') : '';
    $href = 'index.php?action=Media/library/' . $column . '/' . $nextDirection;
    return '<a href="' . $href . '">' . htmlspecialchars($label) . $arrow . '</a>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médiathèque</title>
</head>
<body>
    <p>
        <?php if (isAuthenticated()): ?>
            Connecté en tant que <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> —
            <a href="index.php?action=User/logout">Se déconnecter</a>
        <?php else: ?>
            <a href="index.php?action=User/login">Se connecter</a> |
            <a href="index.php?action=User/signin">S'inscrire</a>
        <?php endif; ?>
    </p>

    <h1>Médiathèque (<?= count($medias) ?> médias)</h1>

    <?php if (!empty($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <?php if (isAuthenticated()): ?>
        <p>
            Ajouter :
            <a href="index.php?action=Media/add/book">un livre</a> |
            <a href="index.php?action=Media/add/movie">un film</a> |
            <a href="index.php?action=Media/add/album">un album</a>
        </p>
    <?php endif; ?>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th><?= sortLink('title', 'Titre', $sortBy, $direction) ?></th>
            <th><?= sortLink('author', 'Auteur', $sortBy, $direction) ?></th>
            <th>Type</th>
            <th>Détails</th>
            <th><?= sortLink('disponible', 'Disponibilité', $sortBy, $direction) ?></th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($medias as $media): ?>
            <tr>
                <td><?= htmlspecialchars($media->getTitle()) ?></td>
                <td><?= htmlspecialchars($media->getAuthor()) ?></td>
                <td><?= $typeLabels[$media->getType()] ?></td>
                <td>
                    <?php if ($media instanceof Book): ?>
                        <?= $media->getPageNumber() ?> pages
                    <?php elseif ($media instanceof Movie): ?>
                        <?= $media->getDuration() ?> min - <?= htmlspecialchars($media->getGender()) ?>
                    <?php elseif ($media instanceof Album): ?>
                        <?= $media->getTrackNumber() ?> pistes - <?= htmlspecialchars($media->getEditor()) ?>
                    <?php endif; ?>
                </td>
                <td><?= $media->isDisponible() ? 'Disponible' : 'Emprunté' ?></td>
                <td>
                    <?php if (isAuthenticated()): ?>
                        <?php if ($media->isDisponible()): ?>
                            <a href="index.php?action=Media/borrow/<?= $media->getId() ?>">Emprunter</a>
                        <?php else: ?>
                            <a href="index.php?action=Media/giveBack/<?= $media->getId() ?>">Rendre</a>
                        <?php endif; ?>
                        |
                        <a href="index.php?action=Media/update/<?= $media->getId() ?>">Modifier</a>
                        |
                        <a href="index.php?action=Media/delete/<?= $media->getId() ?>"
                           onclick="return confirm('Supprimer ce média ?');">Supprimer</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
