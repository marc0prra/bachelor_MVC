<?php
/** @var Media[] $medias */
/** @var array{total:int, available:int, borrowed:int} $stats */

$typeLabels = [
    'book' => 'Livre',
    'movie' => 'Film',
    'album' => 'Album',
];

$title = 'Tableau de bord';
require_once('views/partials/header.php');
?>

<div class="page-header">
    <div>
        <h1>Tableau de bord</h1>
        <span class="count">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?></span>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card card">
        <div class="stat-value"><?= $stats['total'] ?></div>
        <div class="stat-label">Médias au total</div>
    </div>
    <div class="stat-card card">
        <div class="stat-value"><?= $stats['available'] ?></div>
        <div class="stat-label">Disponibles</div>
    </div>
    <div class="stat-card card">
        <div class="stat-value"><?= $stats['borrowed'] ?></div>
        <div class="stat-label">Empruntés</div>
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
    <div class="table-wrap">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medias as $media): ?>
                    <tr>
                        <td>
                            <?php if ($media->getIllustration() !== null): ?>
                                <img class="dashboard-thumb" loading="lazy" decoding="async" src="assets/uploads/media/<?= htmlspecialchars($media->getIllustration()) ?>" alt="Illustration de <?= htmlspecialchars($media->getTitle()) ?>">
                            <?php else: ?>
                                <div class="dashboard-thumb dashboard-thumb-placeholder type-<?= $media->getType() ?>"></div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($media->getTitle()) ?></td>
                        <td><span class="media-type type-<?= $media->getType() ?>"><?= $typeLabels[$media->getType()] ?></span></td>
                        <td><?= htmlspecialchars($media->getAuthor()) ?></td>
                        <td>
                            <?php if ($media->isDisponible()): ?>
                                <span class="badge badge-success">Disponible</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Emprunté</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once('views/partials/footer.php'); ?>
