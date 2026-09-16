<?php
/** @var string|null $error */

$title = 'Inscription';
require_once('views/partials/header.php');
?>

<div class="auth-wrap">
    <div class="card form-card narrow" style="width:100%;">
        <h1>Créer un compte</h1>
        <p style="color:var(--color-text-muted);margin-top:-0.75rem;">Rejoignez la médiathèque en quelques secondes.</p>

        <?php if (!empty($error)): ?>
            <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?action=User/signin" method="post">
            <div class="form-field">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-field">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-field">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
                <small>8 caractères min., une majuscule, une minuscule, un chiffre, un caractère spécial — et sans votre nom d'utilisateur.</small>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
            </div>
        </form>

        <p class="form-foot-link">Déjà inscrit ? <a href="index.php?action=User/login">Se connecter</a></p>
    </div>
</div>

<?php require_once('views/partials/footer.php'); ?>
