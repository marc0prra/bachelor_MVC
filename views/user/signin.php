<?php
/** @var string|null $error */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="index.php?action=User/signin" method="post">
        <div>
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div>
            <label for="email">Adresse e-mail :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </div>
        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <p><small>8 caractères minimum, une majuscule, une minuscule, un chiffre, un caractère spécial, et ne doit pas contenir votre nom d'utilisateur.</small></p>
        </div>

        <input type="submit" value="S'inscrire">
    </form>

    <p>Déjà inscrit ? <a href="index.php?action=User/login">Se connecter</a></p>
</body>
</html>
