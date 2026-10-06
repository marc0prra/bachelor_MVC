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

$search = trim($_GET['q'] ?? '');

function sortLink(string $column, string $label, ?string $sortBy, string $direction, string $search): string {
    $nextDirection = ($sortBy === $column && $direction === 'asc') ? 'desc' : 'asc';
    $isActive = $sortBy === $column;
    $arrow = $isActive ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
    $href = 'index.php?action=Media/library/' . $column . '/' . $nextDirection;
    if ($search !== '') {
        $href .= '&q=' . urlencode($search);
    }
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
        <span class="count"><?= count($medias) ?> média<?= count($medias) > 1 ? 's' : '' ?><?= $search !== '' ? ' trouvé' . (count($medias) > 1 ? 's' : '') . ' pour « ' . htmlspecialchars($search) . ' »' : '' ?></span>
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
    <form class="search-form" method="get" action="index.php">
        <input type="hidden" name="action" value="Media/library<?= $sortBy !== null ? '/' . $sortBy . '/' . $direction : '' ?>">
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
        </svg>
        <input type="search" name="q" placeholder="Rechercher un titre, un auteur…" value="<?= htmlspecialchars($search) ?>">
        <?php if ($search !== ''): ?>
            <a class="search-clear" href="index.php?action=Media/library" aria-label="Effacer la recherche">✕</a>
        <?php endif; ?>
    </form>

    <div class="pill-group">
        <span class="pill pill-label">Trier par</span>
        <?= sortLink('title', 'Titre', $sortBy, $direction, $search) ?>
        <?= sortLink('author', 'Auteur', $sortBy, $direction, $search) ?>
        <?= sortLink('disponible', 'Disponibilité', $sortBy, $direction, $search) ?>
    </div>
</div>

<?php if (empty($medias)): ?>
    <div class="empty-state card">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        <?php if ($search !== ''): ?>
            <p>Aucun résultat pour « <?= htmlspecialchars($search) ?> ».</p>
        <?php else: ?>
            <p>Aucun média pour le moment.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="media-grid">
        <?php foreach ($medias as $media): ?>
            <article class="media-card">
                <?php if ($media->getIllustration() !== null): ?>
                    <img class="media-card-illustration" loading="lazy" decoding="async"
                         src="assets/uploads/media/<?= htmlspecialchars($media->getIllustration()) ?>"
                         alt="Illustration de <?= htmlspecialchars($media->getTitle()) ?>">
                <?php else: ?>
                    <div class="media-card-illustration media-card-illustration-placeholder type-<?= $media->getType() ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                <?php endif; ?>

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
