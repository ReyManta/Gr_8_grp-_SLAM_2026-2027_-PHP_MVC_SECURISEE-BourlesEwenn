<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des utilisateurs</title>
    <link rel="stylesheet" href="<?php echo base_url('css/admin.css'); ?>">
    <link href="<?php echo base_url('images/logo.ico'); ?>" rel="shortcut icon" type="image/x-icon" />
</head>
<body>

    <header class="header">
        <div class="header-left">
            <img src="<?php echo base_url('images/logo.png'); ?>" alt="Logo GSB" class="logo">
            <div>
                <p class="eyebrow">Administration</p>
                <h1>Bonjour, <?= htmlspecialchars($_SESSION['prenom'] ?? 'Administrateur') ?></h1>
            </div>
        </div>
        <div class="header-actions">
            <a href="<?php echo base_url('/'); ?>" class="deconnexion">Déconnexion</a>
        </div>
    </header>

    <div class="admin-content">



        <!-- Sélecteur de type d'utilisateur -->
        <nav class="type-tabs">
            <?php foreach ($typesAutorises as $cle => $libelle): ?>
                <a href="<?php echo base_url('admin?type=' . urlencode($cle)); ?>"
                   class="<?= $cle === $type ? 'active' : '' ?>">
                    <?= htmlspecialchars($libelle) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="admin-layout">

            <!-- Formulaire de création / modification -->
            <div class="form-panel">
                <h2><?= $userAEditer ? "Modifier l'utilisateur" : "Ajouter un utilisateur" ?></h2>
                <form method="post" class="user-form">
                    <input type="hidden" name="action" value="<?= $userAEditer ? 'update' : 'create' ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <?php if ($userAEditer): ?>
                        <input type="hidden" name="id_origine" value="<?= htmlspecialchars($userAEditer['id']) ?>">
                    <?php endif; ?>

                    <label>Identifiant (4 caractères max) :</label>
                    <input type="text" name="id" maxlength="4" required
                           value="<?= htmlspecialchars($userAEditer['id'] ?? '') ?>">

                    <label>Nom :</label>
                    <input type="text" name="nom" required
                           value="<?= htmlspecialchars($userAEditer['nom'] ?? '') ?>">

                    <label>Prénom :</label>
                    <input type="text" name="prenom" required
                           value="<?= htmlspecialchars($userAEditer['prenom'] ?? '') ?>">

                    <label>Login :</label>
                    <input type="text" name="login" required
                           value="<?= htmlspecialchars($userAEditer['login'] ?? '') ?>">

                    <label>Mot de passe <?= $userAEditer ? '(laisser vide pour ne pas le changer)' : '' ?> :</label>
                    <input type="password" name="mdp" <?= $userAEditer ? '' : 'required' ?>>

                    <label>Adresse :</label>
                    <input type="text" name="adresse" required
                           value="<?= htmlspecialchars($userAEditer['adresse'] ?? '') ?>">

                    <label>Code postal :</label>
                    <input type="text" name="cp" maxlength="5" required
                           value="<?= htmlspecialchars($userAEditer['cp'] ?? '') ?>">

                    <label>Ville :</label>
                    <input type="text" name="ville" required
                           value="<?= htmlspecialchars($userAEditer['ville'] ?? '') ?>">

                    <label>Date d'embauche :</label>
                    <input type="date" name="dateEmbauche" required
                           value="<?= htmlspecialchars($userAEditer['dateEmbauche'] ?? '') ?>">

                    <div class="form-actions">
                        <button type="submit"><?= $userAEditer ? 'Enregistrer les modifications' : 'Créer l’utilisateur' ?></button>
                        <?php if ($userAEditer): ?>
                            <a class="btn-annuler" href="<?php echo base_url('admin?type=' . urlencode($type)); ?>">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="list-panel">
                <h2>Liste — <?= htmlspecialchars($typesAutorises[$type]) ?> (<?= count($utilisateurs) ?>)</h2>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Login</th>
                                <th>Ville</th>
                                <th>Embauche</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($utilisateurs)): ?>
                                <tr class="empty-row">
                                    <td colspan="7" class="empty">Aucun utilisateur pour ce type.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($utilisateurs as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['id']) ?></td>
                                        <td><?= htmlspecialchars($u['nom']) ?></td>
                                        <td><?= htmlspecialchars($u['prenom']) ?></td>
                                        <td><?= htmlspecialchars($u['login']) ?></td>
                                        <td><?= htmlspecialchars($u['ville']) ?></td>
                                        <td><?= htmlspecialchars($u['dateEmbauche']) ?></td>
                                        <td class="actions-cell">
                                            <a class="btn-edit" href="<?php echo base_url('admin?type=' . urlencode($type) . '&edit=' . urlencode($u['id'])); ?>">Modifier</a>
                                            <form method="post" class="inline-form"
                                                  onsubmit="return confirm('<?= $type === 'visiteur' ? 'Supprimer définitivement cet utilisateur ainsi que TOUTES ses fiches de frais liées (irréversible) ?' : 'Supprimer définitivement cet utilisateur ?' ?>');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars($u['id']) ?>">
                                                <button type="submit" class="btn-delete">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="footer">
        <p>&copy; 2026 GSB. Tous droits réservés.</p>
        <a href="<?php echo base_url('mentions'); ?>">Mentions légales</a>
    </div>

</body>
</html>