<?php
//acces au controller parent pour l heritage
namespace App\Controllers;
use CodeIgniter\Controller;

//=========================================================================================
//definition d'une classe Controleur (meme nom que votre fichier Controleur.php) 
//heritee de Controller et permettant d'utiliser les raccoucis et fonctions de CodeIgniter
//=========================================================================================

class Controleur extends BaseController {

//=====================================================================
// Page de connexion (ex index.php)
// Gere a la fois l'affichage du formulaire (GET) et son traitement (POST),
// car le formulaire d'origine n'a pas d'action="..." precisee : il revient
// donc sur la même URL.
//=====================================================================
public function index(){

		helper('url');

		$Modele = new \App\Models\Modele();

		$data['errorMessage'] = "";

		if ($this->request->getMethod() === 'post') {

			$username = $this->request->getPost('username');
			$password = $this->request->getPost('password');

			//on essaie dans l'ordre : visiteur, comptable, puis administrateur
			$user = $Modele->connexionVisiteur($username, $password);
			if ($user) {
				session()->set(['prenom' => $user['prenom'], 'id' => $user['id'], 'role' => 'visiteur']);
				return redirect()->to('accueil-medical');
			}

			/* Connexion page Compta
			$user = $Modele->connexionComptable($username, $password);
			if ($user) {
				session()->set(['prenom' => $user['prenom'], 'id' => $user['id'], 'role' => 'comptable']);
				return redirect()->to('compta');
			}
			*/

			/* Connecion page Admin
			$user = $Modele->connexionAdministrateur($username, $password);
			if ($user) {
				session()->set(['prenom' => $user['prenom'], 'id' => $user['id'], 'role' => 'administrateur']);
				return redirect()->to('admin');
			}
			*/
			$data['errorMessage'] = "Identifiants incorrects.";
		}

		echo view('vueIndex', $data);
}


//=====================================================================
// Page d'accueil medicale
// Contrôle de connexion.
//=====================================================================
public function accueilMedical() {

		helper('url');
		if (!$this->estVisiteur()) {
			return redirect()->to('/');
		}

		echo view('vueAccueilMedical');
}


//=====================================================================
// Page "Mes frais"
// Affiche les lignes de frais forfaitaires et hors forfait du visiteur
// connecte pour le mois selectionné (parametre GET "mois").
//=====================================================================
public function frais() {

		helper('url');
		if (!$this->estVisiteur()) {
			return redirect()->to('/');
		}

		$Modele = new \App\Models\Modele();

		$idVisiteur   = session()->get('id');
		$selectedMois = $this->normaliserMois($this->request->getGet('mois') ?? '') ?? date('Ym');
		$currentMonth = \DateTime::createFromFormat('Ym', $selectedMois);

		$data['currentMonthLabel'] = $currentMonth ? $currentMonth->format('F Y') : date('F Y');
		$data['currentMonthValue'] = $currentMonth ? $currentMonth->format('Y-m') : date('Y-m');

		$data['forfaitLignes']     = [];
		$data['horsForfaitLignes'] = [];
		$data['etatFiche']         = 'En attente';
		$data['totalGlobal']       = 0.0;
		$data['error']             = '';

		try {
			$data['forfaitLignes'] = $Modele->getLignesFraisForfait($idVisiteur, $selectedMois);

			$totalForfait = 0.0;
			foreach ($data['forfaitLignes'] as $ligne) {
				$totalForfait += (float) $ligne['total'];
			}

			$data['horsForfaitLignes'] = $Modele->getLignesFraisHorsForfait($idVisiteur, $selectedMois);

			$totalHorsForfait = 0.0;
			foreach ($data['horsForfaitLignes'] as $ligne) {
				$totalHorsForfait += (float) $ligne['montant'];
			}

			$etatRow = $Modele->getEtatFicheFrais($idVisiteur, $selectedMois);
			if ($etatRow) {
				$data['etatFiche'] = $etatRow['etat'];
			}

			$data['totalGlobal'] = $totalForfait + $totalHorsForfait;
		} catch (\Throwable $e) {
			$data['error'] = 'Erreur de lecture des frais : ' . $e->getMessage();
		}

		echo view('vueFrais', $data);
}


//=====================================================================
// Page "Saisie des frais"
// Affiche le formulaire (GET) et enregistre une saisie de frais (POST).
//=====================================================================
public function saisi() {

		helper('url');
		if (!$this->estVisiteur()) {
			return redirect()->to('/');
		}

		$Modele = new \App\Models\Modele();

		$data['message'] = '';
		$data['error']   = '';

		$moisPoste    = $this->request->getPost('mois') ?? date('Ym');
		$selectedMois = $this->parserMois($moisPoste) ?? date('Ym');
		$currentMonth = \DateTime::createFromFormat('Ym', $selectedMois);
		if (!$currentMonth) {
			$currentMonth = new \DateTime();
			$selectedMois = $currentMonth->format('Ym');
		}

		$data['currentMonthLabel'] = $currentMonth->format('F Y');
		$data['currentMonthValue'] = $currentMonth->format('Y-m');
		$data['selectedMois']      = $selectedMois;

		if ($this->request->getMethod() === 'post') {

			$idVisiteur  = session()->get('id');
			$mois        = $this->parserMois($this->request->getPost('mois') ?? '');
			$nuitee      = floatval(str_replace(',', '.', $this->request->getPost('nuitee') ?? '0'));
			$repas       = floatval(str_replace(',', '.', $this->request->getPost('repas') ?? '0'));
			$trajet      = intval($this->request->getPost('trajet') ?? 0);
			$horsForfait = json_decode($this->request->getPost('hors_forfait_json') ?? '[]', true);
			if (!is_array($horsForfait)) {
				$horsForfait = [];
			}

			// Erreurs de saisis

			if (!$mois) {
				$data['error'] = 'Mois invalide.';
			} elseif ($nuitee < 0 || $repas < 0 || $trajet < 0) {
				$data['error'] = 'Les valeurs de frais doivent être positives.';
			} else {
				try {
					if (!$Modele->ficheFraisExiste($idVisiteur, $mois)) {
						$Modele->creerFicheFrais($idVisiteur, $mois, date('Y-m-d'), 'CR');
					}

					if ($trajet > 0) {
						$Modele->insererLigneFraisForfait($idVisiteur, $mois, 'KM', $trajet);
					}

					if ($nuitee > 0) {
						$Modele->insererLigneFraisHorsForfait($idVisiteur, $mois, 'Nuitee', date('Y-m-d'), $nuitee);
					}
					if ($repas > 0) {
						$Modele->insererLigneFraisHorsForfait($idVisiteur, $mois, 'Repas', date('Y-m-d'), $repas);
					}

					foreach ($horsForfait as $item) {
						if (!is_array($item)) {
							continue;
						}
						$libelle  = trim($item['libelle'] ?? '');
						$dateItem = trim($item['date'] ?? '');
						$montant  = isset($item['montant']) ? floatval(str_replace(',', '.', $item['montant'])) : 0.0;
						if ($libelle === '' || $dateItem === '' || $montant <= 0) {
							continue;
						}
						$Modele->insererLigneFraisHorsForfait($idVisiteur, $mois, $libelle, $dateItem, $montant);
					}

					$data['message'] = 'Les frais ont ete enregistres en base de donnees.';
				} catch (\Throwable $e) {
					$data['error'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
				}
			}
		}

		echo view('vueSaisi', $data);
}


//=====================================================================
// Espace comptabilite
//=====================================================================
public function compta() {

		helper('url');
			if (!$this->estConnecte() || session()->get('role') !== 'comptable') {
			return redirect()->to('/');
		}

		echo view('vueComptable');
}


//=====================================================================
// Page admin
//=====================================================================
public function admin() {

		helper('url');
		if (!$this->estConnecte() || session()->get('role') !== 'administrateur') {
			return redirect()->to('/');
		}

		$Modele = new \App\Models\Modele();

		// Liste blanche : jamais de nom de table venant directement de l'utilisateur
		$typesAutorises = [
			'visiteur'       => 'Visiteur medical',
			'comptable'      => 'Comptable',
			'administrateur' => 'Administrateur',
		];
		$tablesReelles = [
			'visiteur'       => 'Visiteur',
			'comptable'      => 'Comptable',
			'administrateur' => 'Administrateur',
		];

		$type = $this->request->getGet('type') ?? 'visiteur';
		if (!array_key_exists($type, $typesAutorises)) {
			$type = 'visiteur';
		}

		if ($this->request->getMethod() === 'post') {

			$action   = $this->request->getPost('action') ?? '';
			$postType = $this->request->getPost('type') ?? 'visiteur';
			if (!array_key_exists($postType, $typesAutorises)) {
				$postType = 'visiteur';
			}
			$table = $postType;

			if ($action === 'create' || $action === 'update') {

				$id           = trim($this->request->getPost('id') ?? '');
				$nom          = trim($this->request->getPost('nom') ?? '');
				$prenom       = trim($this->request->getPost('prenom') ?? '');
				$login        = trim($this->request->getPost('login') ?? '');
				$mdp          = $this->request->getPost('mdp') ?? '';
				$adresse      = trim($this->request->getPost('adresse') ?? '');
				$cp           = trim($this->request->getPost('cp') ?? '');
				$ville        = trim($this->request->getPost('ville') ?? '');
				$dateEmbauche = trim($this->request->getPost('dateEmbauche') ?? '');

				if ($id === '' || $nom === '' || $prenom === '' || $login === '' || $adresse === '' || $cp === '' || $ville === '' || $dateEmbauche === '') {
					$erreur = "Tous les champs sont obligatoires (sauf le mot de passe lors d'une modification si vous ne souhaitez pas le changer).";
				} elseif (strlen($id) > 4) {
					$erreur = "L'identifiant ne peut pas depasser 4 caracteres.";
				} elseif ($action === 'create' && $mdp === '') {
					$erreur = "Le mot de passe est obligatoire e la creation.";
				} else {
					try {
						if ($action === 'create') {
							$Modele->creerUtilisateur($tablesReelles[$table], $id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche);
							$message = "Utilisateur cree avec succes.";
						} else {
							$idOrigine = trim($this->request->getPost('id_origine') ?? $id);

							if ($mdp !== '') {
								$Modele->modifierUtilisateurAvecMdp($tablesReelles[$table], $id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche, $idOrigine);
							} else {
								$Modele->modifierUtilisateurSansMdp($tablesReelles[$table], $id, $nom, $prenom, $login, $adresse, $cp, $ville, $dateEmbauche, $idOrigine);
							}
							$message = "Utilisateur modifie avec succes.";
						}
					} catch (\Throwable $e) {
						if (str_contains($e->getMessage(), 'Duplicate entry')) {
							$erreur = "Cet identifiant existe deje pour ce type d'utilisateur.";
						} else {
							$erreur = "Erreur lors de l'enregistrement : " . $e->getMessage();
						}
					}
				}

			} elseif ($action === 'delete') {

				$id = trim($this->request->getPost('id') ?? '');

				if ($table === 'administrateur' && $id === session()->get('id')) {
					$erreur = "Vous ne pouvez pas supprimer votre propre compte administrateur.";
				} elseif ($id !== '') {
					try {
						if ($table === 'visiteur') {
							// Un visiteur peut avoir des fiches de frais liees : on supprime la
							// chaîne dans l'ordre pour respecter les cles etrangeres.
							$Modele->supprimerLignesFraisForfait($id);
							$Modele->supprimerLignesFraisHorsForfait($id);
							$Modele->supprimerFicheFraisParVisiteur($id);
						}

						$Modele->supprimerUtilisateur($tablesReelles[$table], $id);
						$message = "Utilisateur supprime avec succes (ainsi que ses fiches de frais liees, le cas echeant).";
					} catch (\Throwable $e) {
						if (str_contains($e->getMessage(), 'foreign key constraint')) {
							$erreur = "Impossible de supprimer cet utilisateur : des donnees liees existent encore ailleurs dans la base.";
						} else {
							$erreur = "Erreur lors de la suppression : " . $e->getMessage();
						}
					}
				}
			}

			session()->setFlashdata('message', $message ?? '');
			session()->setFlashdata('erreur', $erreur ?? '');
			return redirect()->to('admin?type=' . urlencode($table));
		}

		$data['message'] = session()->getFlashdata('message') ?? '';
		$data['erreur']  = session()->getFlashdata('erreur') ?? '';

		$userAEditer = null;
		$idAEditer   = $this->request->getGet('edit');
		if ($idAEditer !== null) {
			$userAEditer = $Modele->getUtilisateurParId($tablesReelles[$type], $idAEditer);
		}

		$data['type']           = $type;
		$data['typesAutorises'] = $typesAutorises;
		$data['userAEditer']    = $userAEditer;
		$data['utilisateurs']   = $Modele->getUtilisateursParType($tablesReelles[$type]);

		echo view('vueAdmin', $data);
}


//=====================================================================
// Mentions legales
//=====================================================================
public function mentions() {
		echo view('vueMentions');
}


//=====================================================================
// Fonctions autres
//=====================================================================

// Verifie que l'utilisateur est connecte
private function estConnecte(): bool {
		return session()->get('id') !== null;
}

// Normalise un mois reçu en GET vers le format YYYYMM
private function normaliserMois(string $mois): ?string {
		$mois = trim($mois);
		if (preg_match('/^(\d{4})-(\d{2})$/', $mois, $matches)) {
			return $matches[1] . $matches[2];
		}
		if (preg_match('/^(\d{6})$/', $mois, $matches)) {
			return $matches[1];
		}
		return null;
}

// Interprete un mois reçu en POST, y compris en toutes lettres
private function parserMois(string $mois): ?string {
		$mois = trim($mois);
		if (preg_match('/^[0-9]{6}$/', $mois)) {
			return $mois;
		}
		if (preg_match('/^([0-9]{4})-([0-9]{2})$/', $mois, $matches)) {
			return $matches[1] . $matches[2];
		}
		$mois = mb_strtolower(trim(preg_replace('/\s+/', ' ', $mois)), 'UTF-8');
		$map = [
			'janvier' => '01', 'fevrier' => '02', 'fevrier' => '02', 'mars' => '03', 'avril' => '04',
			'mai' => '05', 'juin' => '06', 'juillet' => '07', 'août' => '08', 'aout' => '08',
			'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'decembre' => '12',
		];
		if (preg_match('/^([a-zeâeeêôûùç]+)\s+([0-9]{4})$/u', $mois, $matches)) {
			$nomMois = $matches[1];
			$annee   = $matches[2];
			if (isset($map[$nomMois])) {
				return $annee . $map[$nomMois];
			}
		}
		return null;
}

private function estVisiteur(): bool{
	return session()->get('id') !== null && session()->get('role') === 'visiteur';
}
//fin de la classe
}