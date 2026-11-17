<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisie des frais</title>
<link rel="stylesheet" href="<?php echo base_url('css/saisi.css'); ?>">
    <link href="<?php echo base_url('images/logo.ico'); ?>" rel="shortcut icon" type="image/x-icon" />
</head>
<body>
    <div class="header">
        <img src="<?php echo base_url('images/logo.png'); ?>" alt="Logo GSB" class="logo">
        <h1>Saisie des frais</h1>
        <a href="<?php echo base_url('/'); ?>" class="deconnexion">Déconnexion</a>
    </div>

    <div class="container">
        <form id="form-saisi" method="post" action="<?php echo base_url('saisi'); ?>">
            <input type="hidden" name="mois" id="mois-input" value="<?= htmlspecialchars($selectedMois) ?>">
            <input type="hidden" name="hors_forfait_json" id="hors-forfait-json" value="">
            <?php if ($message): ?>
                <div style="padding:12px; margin-bottom:16px; background:#e7f7e7; border:1px solid #3c763d; color:#2a5422;">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php elseif ($error): ?>
                <div style="padding:12px; margin-bottom:16px; background:#f9e7e7; border:1px solid #a94442; color:#a94442;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="top-section">
                <div class="menu-dropdown">
                <button class="dropdown-btn">☰ Menu</button>
                <div class="dropdown-content">
                    <a href="<?php echo base_url('accueil-medical'); ?>">Accueil</a>
                    <a href="<?php echo base_url('frais'); ?>">Mes frais</a>
                    <a href="<?php echo base_url('saisi'); ?>" class="active">Saisie des frais</a>
                </div>
            </div>

            <div class="month-selector">
                <label>Mois :</label>
                <div class="month-dropdown">
                    <button type="button" class="month-btn" id="current-month" data-month="<?= htmlspecialchars($currentMonthValue) ?>"><?= htmlspecialchars($currentMonthLabel) ?></button>
                    <div class="month-list"></div>
                </div>
            </div>
        </div>

        <div class="frais-section">
            <div class="forfait-card">
                <h2 class="card-title">Frais forfaitaire</h2>
                <div class="forfait-grid">
                    <div class="forfait-item">
                        <label for="nuitee">Nuitée</label>
                        <input type="number" id="nuitee" name="nuitee" placeholder="0" min="0" step="0.01">
                        <span class="unit">€</span>
                    </div>
                    <div class="forfait-item">
                        <label for="repas">Repas</label>
                        <input type="number" id="repas" name="repas" placeholder="0" min="0" step="0.01">
                        <span class="unit">€</span>
                    </div>
                    <div class="forfait-item">
                        <label for="trajet">Trajet</label>
                        <input type="number" id="trajet" name="trajet" placeholder="0" min="0" step="1" inputmode="numeric" pattern="[0-9]*">
                        <span class="unit">km</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="hors-forfait-section">
            <div class="hors-forfait-card">
                <h2 class="card-title">Hors forfait</h2>
                <div class="hors-forfait-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="libelle">Libellé</label>
                            <input type="text" id="libelle" placeholder="Description du frais">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date-frais">Date</label>
                            <input type="date" id="date-frais">
                        </div>
                        <div class="form-group">
                            <label for="montant-frais">Montant</label>
                            <input type="number" id="montant-frais" placeholder="0" min="0" step="0.01" inputmode="decimal" pattern="[0-9]*([\.,][0-9]+)?">
                            <span class="unit">€</span>
                        </div>
                    </div>
                    <button type="button" class="btn-ajouter" id="btn-ajouter">Ajouter frais</button>
                </div>

                <div class="hors-forfait-list">
                    <h3>Frais ajoutés</h3>
                    <table class="hors-forfait-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Libellé</th>
                                <th>Montant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="footer-section">
            <button type="submit" class="btn-enregistrer">Enregistrer frais</button>
        </div>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2026 GSB. Tous droits réservés.</p>
        <a href="<?php echo base_url('mentions'); ?>">Mentions légales</a>
    </div>

    <script>
        // Stockage des frais hors forfait
let fraisHorsForfait = [];

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date-frais');
    if (dateInput) {
        dateInput.valueAsDate = new Date();
    }

    const monthBtn = document.getElementById('current-month');
    const monthList = document.querySelector('.month-list');
    const hiddenMois = document.getElementById('mois-input');

    if (monthBtn && monthList) {
        setupMonthDropdown(monthBtn, monthList);
        generateMonthList(monthList, monthBtn, hiddenMois);
    }

    const btnAjouter = document.getElementById('btn-ajouter');
    if (btnAjouter) {
        btnAjouter.addEventListener('click', ajouterFrais);
    }

    const btnEnregistrer = document.querySelector('.btn-enregistrer');
    if (btnEnregistrer) {
        btnEnregistrer.addEventListener('click', enregistrerFrais);
    }

    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let newValue = e.target.value;
            newValue = newValue.replace(/[^0-9.,]/g, '');
            const parts = newValue.split(/[.,]/);
            if (parts.length > 2) {
                newValue = parts[0] + '.' + parts.slice(1).join('');
            }
            if (newValue.includes(',')) {
                newValue = newValue.replace(',', '.');
            }
            if (e.target.id === 'trajet') {
                newValue = newValue.split('.')[0];
            }
            e.target.value = newValue;
        });

        input.addEventListener('keydown', function(e) {
            const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
            if (allowed.includes(e.key)) return;
            if (e.key === '.' || e.key === ',') {
                if (e.target.id === 'trajet') {
                    e.preventDefault();
                }
                return;
            }
            if (!/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });
    });
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

function generateMonthList(monthList, monthBtn, hiddenMois) {
    const today = new Date();
    const selectedValue = hiddenMois && hiddenMois.value
        ? hiddenMois.value.replace(/^([0-9]{4})([0-9]{2})$/, '$1-$2')
        : today.toISOString().slice(0, 7);

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
            monthBtn.textContent = button.textContent;
            monthBtn.dataset.month = monthValue;
            if (hiddenMois) {
                hiddenMois.value = monthValue.replace('-', '');
            }
        }

        button.addEventListener('click', function(e) {
            e.stopPropagation();
            selectMonth(this, monthBtn, monthList, hiddenMois);
        });

        monthList.appendChild(button);
    });
}

function selectMonth(button, monthBtn, monthList, hiddenMois) {
    const monthName = button.textContent;
    monthBtn.textContent = monthName;
    monthBtn.dataset.month = button.dataset.month;

    if (hiddenMois && button.dataset.month) {
        hiddenMois.value = button.dataset.month.replace('-', '');
    }

    document.querySelectorAll('.month-list button').forEach(btn => {
        btn.classList.remove('selected');
    });
    button.classList.add('selected');
    monthList.classList.remove('active');
}

function getMonthName(monthIndex) {
    const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    return months[monthIndex];
}

function ajouterFrais() {
    const libelle = document.getElementById('libelle').value.trim();
    const date = document.getElementById('date-frais').value;
    const montant = parseFloat(document.getElementById('montant-frais').value) || 0;

    if (!libelle) {
        alert('Veuillez entrer un libellé');
        return;
    }
    if (!date) {
        alert('Veuillez sélectionner une date');
        return;
    }
    if (montant <= 0) {
        alert('Veuillez entrer un montant valide');
        return;
    }

    const frais = {
        id: Date.now(),
        date: date,
        libelle: libelle,
        montant: montant
    };

    fraisHorsForfait.push(frais);
    document.getElementById('libelle').value = '';
    document.getElementById('date-frais').valueAsDate = new Date();
    document.getElementById('montant-frais').value = '';
    afficherFraisHorsForfait();
}

function afficherFraisHorsForfait() {
    const tableBody = document.querySelector('.hors-forfait-table tbody');
    if (!tableBody) {
        return;
    }

    if (fraisHorsForfait.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #999;">Aucun frais ajouté</td></tr>';
        return;
    }

    tableBody.innerHTML = '';
    fraisHorsForfait.forEach(frais => {
        const row = document.createElement('tr');
        const dateObj = new Date(frais.date + 'T00:00:00');
        const dateFormatee = dateObj.toLocaleDateString('fr-FR');

        row.innerHTML = `
            <td>${dateFormatee}</td>
            <td>${frais.libelle}</td>
            <td>${frais.montant.toFixed(2).replace('.', ',')} €</td>
            <td>
                <button type="button" class="btn-supprimer" onclick="supprimerFrais(${frais.id})">Supprimer</button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

function supprimerFrais(id) {
    fraisHorsForfait = fraisHorsForfait.filter(frais => frais.id !== id);
    afficherFraisHorsForfait();
}

function enregistrerFrais() {
    const nuitee = parseFloat(document.getElementById('nuitee').value) || 0;
    const repas = parseFloat(document.getElementById('repas').value) || 0;
    const trajet = parseFloat(document.getElementById('trajet').value) || 0;
    const moisInput = document.getElementById('mois-input');
    const mois = moisInput ? moisInput.value : '';

    const donneesFrais = {
        mois: mois,
        forfait: {
            nuitee: nuitee,
            repas: repas,
            trajet: trajet,
            total: nuitee + repas + trajet
        },
        horsForfait: fraisHorsForfait,
        totalHorsForfait: fraisHorsForfait.reduce((sum, f) => sum + f.montant, 0)
    };

    donneesFrais.totalGeneral = donneesFrais.forfait.total + donneesFrais.totalHorsForfait;
    console.log('Frais enregistrés:', donneesFrais);
    alert(`Frais enregistrés pour ${mois}\nTotal: ${donneesFrais.totalGeneral.toFixed(2)} €`);
}

    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-saisi');
        if (!form) {
            return;
        }

        form.addEventListener('submit', function() {
            const horsField = document.getElementById('hors-forfait-json');
            const monthField = document.getElementById('mois-input');
            if (horsField) {
                horsField.value = JSON.stringify(window.fraisHorsForfait || []);
            }
            if (monthField) {
                const monthButton = document.getElementById('current-month');
                let monthValue = '';
                if (monthButton) {
                    monthValue = monthButton.dataset.month || monthButton.textContent;
                }
                monthField.value = monthValue.replace('-', '');
            }
        });
    });
    </script>
</body>
</html>