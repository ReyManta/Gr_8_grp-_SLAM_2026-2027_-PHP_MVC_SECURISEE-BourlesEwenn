<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Mes frais</title>
<link rel="stylesheet" href="<?php echo base_url('css/frais.css'); ?>">
    <link href="<?php echo base_url('images/logo.ico'); ?>" rel="shortcut icon" type="image/x-icon" />
  </head>
  <body>
    <div class="header">
      <img src="<?php echo base_url('images/logo.png'); ?>" alt="Logo GSB" class="logo">
      <h1>Mes frais</h1>
      <a href="<?php echo base_url('/'); ?>" class="deconnexion">Déconnexion</a>
    </div>

    <div class="container">
      <!-- Menu déroulant en haut à gauche -->
      <div class="top-section">
        <div class="menu-dropdown">
          <button class="dropdown-btn">☰ Menu</button>
          <div class="dropdown-content">
            <a href="<?php echo base_url('accueil-medical'); ?>">Accueil</a>
            <a href="<?php echo base_url('frais'); ?>" class="active">Mes frais</a>
            <a href="<?php echo base_url('saisi'); ?>">Saisie des frais</a>
          </div>
        </div>

        <!-- Sélecteur de mois en haut à droite -->
        <div class="month-selector">
          <label>Mois :</label>
          <div class="month-dropdown">
            <button type="button" class="month-btn" id="current-month" data-month="<?= htmlspecialchars($currentMonthValue) ?>"><?= htmlspecialchars($currentMonthLabel) ?></button>
            <div class="month-list">
              <!-- Les mois seront ajoutés par JavaScript -->
            </div>
          </div>
        </div>
      </div>

      <!-- Tableau des frais -->
      <div class="frais-section">
        <h2>Frais du mois sélectionné</h2>
        <?php if ($error): ?>
            <div style="padding:12px; margin-bottom:16px; background:#f9e7e7; border:1px solid #a94442; color:#a94442;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <table class="frais-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Description</th>
              <th>Catégorie</th>
              <th>Montant</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($forfaitLignes) && empty($horsForfaitLignes)): ?>
                <tr class="empty-row">
                  <td colspan="5" style="text-align: center; color: #999;">Aucun frais pour ce mois</td>
                </tr>
            <?php else: ?>
                <?php foreach ($forfaitLignes as $ligne): ?>
                    <tr>
                      <td>-</td>
                      <td><?= htmlspecialchars($ligne['libelle']) ?></td>
                      <td>Forfait</td>
                      <td><?= number_format($ligne['total'], 2, ',', ' ') ?> €</td>
                      <td><?= htmlspecialchars($etatFiche) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ($horsForfaitLignes as $ligne): ?>
                    <tr>
                      <td><?= htmlspecialchars((new DateTime($ligne['date']))->format('d/m/Y')) ?></td>
                      <td><?= htmlspecialchars($ligne['libelle']) ?></td>
                      <td>Hors forfait</td>
                      <td><?= number_format($ligne['montant'], 2, ',', ' ') ?> €</td>
                      <td><?= htmlspecialchars($etatFiche) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Total et état des frais -->
      <div class="summary-section">
        <div class="summary-card">
          <div class="summary-item">
            <label>Total des frais :</label>
            <span class="total-amount"><?= number_format($totalGlobal, 2, ',', ' ') ?> €</span>
          </div>
          <div class="summary-item">
            <label>État des frais :</label>
            <span class="status-badge"><?= htmlspecialchars($etatFiche) ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="footer">
        <p>&copy; 2026 GSB. Tous droits réservés.</p>
        <a href="<?php echo base_url('mentions'); ?>">Mentions légales</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const monthBtn = document.getElementById('current-month');
    const monthList = document.querySelector('.month-list');

    if (monthBtn && monthList) {
        setupMonthDropdown(monthBtn, monthList);
        generateMonthList(monthList, monthBtn);
    }
});

function setupMonthDropdown(monthBtn, monthList) {
    monthBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        monthList.classList.toggle('active');
    });

    monthList.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    document.addEventListener('click', function() {
        monthList.classList.remove('active');
    });
}

function generateMonthList(monthList, monthBtn) {
    const today = new Date();
    const selectedValue = monthBtn.dataset.month || today.toISOString().slice(0, 7);
    const months = [];

    for (let offset = -4; offset <= 6; offset++) {
        months.push(new Date(today.getFullYear(), today.getMonth() + offset, 1));
    }

    monthList.innerHTML = '';

    months.forEach(date => {
        const button = document.createElement('button');
        button.type = 'button';
        const monthName = getMonthName(date.getMonth());
        const year = date.getFullYear();
        const monthValue = `${year}-${String(date.getMonth() + 1).padStart(2, '0')}`;

        button.textContent = `${monthName} ${year}`;
        button.dataset.month = monthValue;

        if (monthValue === selectedValue) {
            button.classList.add('selected');
        }

        button.addEventListener('click', function(e) {
            e.stopPropagation();
            selectMonth(this, monthBtn, monthList);
        });

        monthList.appendChild(button);
    });
}

function selectMonth(button, monthBtn, monthList) {
    monthBtn.textContent = button.textContent;
    monthBtn.dataset.month = button.dataset.month;

    document.querySelectorAll('.month-list button').forEach(btn => {
        btn.classList.remove('selected');
    });
    button.classList.add('selected');
    monthList.classList.remove('active');

    const url = new URL(window.location.href);
    url.searchParams.set('mois', button.dataset.month.replace('-', ''));
    window.location.href = url.toString();
}

function getMonthName(monthIndex) {
    const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    return months[monthIndex];
}

    </script>
  </body>
</html>