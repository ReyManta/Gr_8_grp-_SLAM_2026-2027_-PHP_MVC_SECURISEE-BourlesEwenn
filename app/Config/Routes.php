<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Page de connexion (ex index.php) : une seule méthode répond au GET (affichage)
// et au POST (traitement du formulaire), car le formulaire n'a pas d'action="..." précisée.
$routes->get('/', 'Controleur::index');
$routes->post('/', 'Controleur::index');

// Espace visiteur médical
$routes->get('accueil-medical', 'Controleur::accueilMedical');
$routes->get('frais', 'Controleur::frais');
$routes->get('saisi', 'Controleur::saisi');
$routes->post('saisi', 'Controleur::saisi');

// Espace comptable
$routes->get('compta', 'Controleur::compta');

// Espace administrateur
$routes->get('admin', 'Controleur::admin');
$routes->post('admin', 'Controleur::admin');

// Page publique
$routes->get('mentions', 'Controleur::mentions');