<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="<?php echo base_url('css/accueil_medical.css'); ?>">
    <link href="<?php echo base_url('images/logo.ico'); ?>" rel="shortcut icon" type="image/x-icon" />
  </head>
    <body>
    <!-- Header -->
    <header class="header">
      <div class="header-left">
        <img src="<?php echo base_url('images/logo.png'); ?>" alt="Logo GSB" class="logo">
        <div>
          <p class="eyebrow">Accueil médicale</p>
          <h1>Bonjour, <?php echo htmlspecialchars($_SESSION['prenom'] ?? 'Utilisatrice'); ?></h1>
        </div>
      </div>
      <div class="header-actions">
        <a href="<?php echo base_url('/'); ?>" class="deconnexion">Déconnexion</a>
      </div>
    </header>

    <!-- Menu Navigation -->
    <div class="menu-dropdown">
      <button class="dropdown-btn" aria-expanded="false" aria-haspopup="true">☰ Menu</button>
      <div class="dropdown-content" role="menu">
        <a href="<?php echo base_url('accueil-medical'); ?>" class="active" role="menuitem">Accueil</a>
        <a href="<?php echo base_url('frais'); ?>" role="menuitem">Mes frais</a>
        <a href="<?php echo base_url('saisi'); ?>" role="menuitem">Saisie des frais</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Consultation Section -->
      <div class="consultation_frais">
        <div class="mois">
          <label for="mois-select">Période :</label>
          <select id="mois-select" aria-label="Sélectionner une période">
            <option value="">-- Sélectionner mois/année --</option>
            <option value="Janv 2026">Janv 2026</option>
            <option value="Fév 2026">Fév 2026</option>
            <option value="Mars 2026">Mars 2026</option>
            <option value="Avr 2026">Avr 2026</option>
            <option value="Mai 2026">Mai 2026</option>
            <option value="Juin 2026">Juin 2026</option>
            <option value="Juil 2026">Juil 2026</option>
            <option value="Août 2026">Août 2026</option>
            <option value="Sept 2026">Sept 2026</option>
            <option value="Oct 2026">Oct 2026</option>
            <option value="Nov 2026">Nov 2026</option>
            <option value="Déc 2026">Déc 2026</option>
          </select>
        </div>
        
        <div class="etat">
          <label>État :</label>
          <div class="etat-static" id="etat-display">En cours</div>
        </div>
        
        <div class="total_frais">
          <label>Total des frais :</label>
          <span id="total-frais" class="total-amount">0 €</span>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>&copy; 2026 GSB. Tous droits réservés.</p>
      <a href="<?php echo base_url('mentions'); ?>">Mentions légales</a>
    </div>

    <script>
        // Burger menu toggle
const burger = document.querySelector('.burger');
const nav = document.querySelector('nav');
const navLinks = document.querySelectorAll('nav .nav-link');

if (burger) {
  burger.addEventListener('click', () => {
    burger.classList.toggle('active');
    nav.classList.toggle('active');
  });

  // Close menu when a link is clicked
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      burger.classList.remove('active');
      nav.classList.remove('active');
    });
  });
  // Fermer le dropdown quand on clique ailleurs
    document.addEventListener('click', function() {
        const monthList = document.querySelector('.month-list');
        monthList.classList.remove('active');
    });
}

    </script>
  </body>
</html>