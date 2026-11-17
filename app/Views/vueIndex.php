<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="<?php echo base_url('css/index.css'); ?>">
    <link href="<?php echo base_url('images/logo.ico'); ?>" rel="shortcut icon" type="image/x-icon" />
</head>

<body>

    <div class="header">
        <img src="<?php echo base_url('images/logo.png'); ?>" alt="Logo GSB" class="logo">
        <h1>GSB</h1>
    </div>

    <div class="connexion">
        <h2>Connexion</h2>

        <!-- AFFICHAGE ERREUR -->
        <?php if ($errorMessage !== "") : ?>
            <p style="color:red;"><?= $errorMessage ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2026 GSB. Tous droits réservés.</p>
        <a href="<?php echo base_url('mentions'); ?>">Mentions légales</a>
    </div>

</body>
</html>