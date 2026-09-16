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
    $isActive = $sortBy === $column;
    $arrow = $isActive ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
    $href = 'index.php?action=Media/library/' . $column . '/' . $nextDirection;
    $class = 'pill' . ($isActive ? ' is-active' : '');
    return '<a class="' . $class . '" href="' . $href . '">' . htmlspecialchars($label) . $arrow . '</a>';
}

$title = 'Médiathèque';
require_once('views/partials/header.php');
?>

<?php if (!empty($message)): ?>
    <div class="flash flash-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>La médiathèque</h1>
        <span class="count"><?= count($medias) ?> média<?= count($medias) > 1 ? 's' : '' ?></span>
    </div>

    <?php if (isAuthenticated()): ?>
        <div class="pill-group">
            <a class="btn btn-primary" href="index.php?action=Media/add/book">+ Livre</a>
            <a class="btn btn-primary" href="index.php?action=Media/add/movie">+ Film</a>
            <a class="btn btn-primary" href="index.php?action=Media/add/album">+ Album</a>
        </div>
    <?php endif; ?>
</div>

<div class="toolbar">
    <div class="pill-group">
        <span class="pill" style="border:none;background:none;padding-left:0;">Trier par</span>
        <?= sortLink('title', 'Titre', $sortBy, $direction) ?>
        <?= sortLink('author', 'Auteur', $sortBy, $direction) ?>
        <?= sortLink('disponible', 'Disponibilité', $sortBy, $direction) ?>
    </div>
</div>

<?php if (empty($medias)): ?>
    <div class="empty-state card">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        <p>Aucun média pour le moment.</p>
    </div>
<?php else: ?>
    <div class="media-grid">
        <?php foreach ($medias as $media): ?>
            <article class="media-card">
                <div class="media-card-top">
                    <span class="media-type type-<?= $media->getType() ?>"><?= $typeLabels[$media->getType()] ?></span>
                    <?php if ($media->isDisponible()): ?>
                        <span class="badge badge-success">Disponible</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Emprunté</span>
                    <?php endif; ?>
                </div>

                <div>
                    <h3><?= htmlspecialchars($media->getTitle()) ?></h3>
                    <div class="author"><?= htmlspecialchars($media->getAuthor()) ?></div>
                </div>

                <div class="details">
                    <?php if ($media instanceof Book): ?>
                        <?= $media->getPageNumber() ?> pages
                    <?php elseif ($media instanceof Movie): ?>
                        <?= $media->getDuration() ?> min · <?= htmlspecialchars($media->getGender()) ?>
                    <?php elseif ($media instanceof Album): ?>
                        <?= $media->getTrackNumber() ?> pistes · <?= htmlspecialchars($media->getEditor()) ?>
                    <?php endif; ?>
                </div>

                <?php if (isAuthenticated()): ?>
                    <div class="media-card-actions">
                        <?php if ($media->isDisponible()): ?>
                            <a class="btn btn-ghost" href="index.php?action=Media/borrow/<?= $media->getId() ?>">Emprunter</a>
                        <?php else: ?>
                            <a class="btn btn-ghost" href="index.php?action=Media/giveBack/<?= $media->getId() ?>">Rendre</a>
                        <?php endif; ?>
                        <a class="btn btn-ghost" href="index.php?action=Media/update/<?= $media->getId() ?>">Modifier</a>
                        <a class="btn btn-danger" href="index.php?action=Media/delete/<?= $media->getId() ?>"
                           data-confirm="Supprimer « <?= htmlspecialchars($media->getTitle(), ENT_QUOTES) ?> » ?">Supprimer</a>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once('views/partials/footer.php'); ?>
