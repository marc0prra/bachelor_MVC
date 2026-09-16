<?php
/** @var string|null $error */
/** @var string|null $message */

$title = 'Connexion';
require_once('views/partials/header.php');
?>

<div class="auth-wrap">
    <div class="card form-card narrow" style="width:100%;">
        <h1>Content de vous revoir</h1>
        <p style="color:var(--color-text-muted);margin-top:-0.75rem;">Connectez-vous pour gérer la médiathèque.</p>

        <?php if (!empty($message)): ?>
            <div class="flash flash-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?action=User/login" method="post">
            <div class="form-field">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </div>
        </form>

        <p class="form-foot-link">Pas encore de compte ? <a href="index.php?action=User/signin">S'inscrire</a></p>
    </div>
</div>

<?php require_once('views/partials/footer.php'); ?>
