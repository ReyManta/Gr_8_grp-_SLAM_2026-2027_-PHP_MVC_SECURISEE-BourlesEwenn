<?php
//acces au Modele parent pour l heritage
namespace App\Models;
use CodeIgniter\Model;

//=========================================================================================
//définition d'une classe Modele (meme nom que votre fichier Modele.php) 
//héritée de Modele et permettant d'utiliser les raccoucis et fonctions de CodeIgniter
//=========================================================================================
class Modele extends Model {

//==========================
// Code du modele
//==========================

//=========================================================================================
//=========================================================================================
// FONCTIONS ISSUES DES REQUETES SQL DU PROJET Site-GSB (index.php, admin.php, frais.php, saisi.php)
//=========================================================================================
//=========================================================================================


//=========================================================================
// ---- index.php : connexion (authentification) ----
//=========================================================================

//=========================================================================
// Vérifie les identifiants d'un visiteur médical
// Requete d'origine : SELECT * FROM Visiteur WHERE login = ? AND mdp = ?
//=========================================================================
public function connexionVisiteur($login, $mdp) {

	$db = db_connect();

	$sql = 'SELECT * FROM Visiteur WHERE login = ? AND mdp = ?';

	$resultat = $db->query($sql, [$login, $mdp]);

	// un seul utilisateur correspond (ou aucun) -> on renvoie une ligne
	return $resultat->getRowArray();

}


//=========================================================================
// Vérifie les identifiants d'un comptable
// Requete d'origine : SELECT * FROM Comptable WHERE login = ? AND mdp = ?
//=========================================================================
public function connexionComptable($login, $mdp) {

	$db = db_connect();

	$sql = 'SELECT * FROM Comptable WHERE login = ? AND mdp = ?';

	$resultat = $db->query($sql, [$login, $mdp]);

	return $resultat->getRowArray();

}


//=========================================================================
// Vérifie les identifiants d'un administrateur
// Requete d'origine : SELECT * FROM Administrateur WHERE login = ? AND mdp = ?
//=========================================================================
public function connexionAdministrateur($login, $mdp) {

	$db = db_connect();

	$sql = 'SELECT * FROM Administrateur WHERE login = ? AND mdp = ?';

	$resultat = $db->query($sql, [$login, $mdp]);

	return $resultat->getRowArray();

}


//=========================================================================
// ---- admin.php : gestion des utilisateurs (Visiteur / Comptable / Administrateur) ----
// Rappel : $table doit être un nom de table déjà filtré par une liste blanche
// (voir $tablesReelles dans admin.php), jamais une valeur brute venant de l'utilisateur.
//=========================================================================

//=========================================================================
// Crée un utilisateur (visiteur, comptable ou administrateur)
// Requete d'origine :
// INSERT INTO {table} (id, nom, prenom, login, mdp, adresse, cp, ville, dateEmbauche)
// VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
//=========================================================================
public function creerUtilisateur($table, $id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche) {

	$db = db_connect();

	$sql = "INSERT INTO {$table} (id, nom, prenom, login, mdp, adresse, cp, ville, dateEmbauche)
	        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

	return $db->query($sql, [$id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche]);

}


//=========================================================================
// Modifie un utilisateur en changeant aussi son mot de passe
// Requete d'origine :
// UPDATE {table} SET id=?, nom=?, prenom=?, login=?, mdp=?, adresse=?, cp=?, ville=?, dateEmbauche=? WHERE id=?
//=========================================================================
public function modifierUtilisateurAvecMdp($table, $id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche, $idOrigine) {

	$db = db_connect();

	$sql = "UPDATE {$table} SET id=?, nom=?, prenom=?, login=?, mdp=?, adresse=?, cp=?, ville=?, dateEmbauche=? WHERE id=?";

	return $db->query($sql, [$id, $nom, $prenom, $login, $mdp, $adresse, $cp, $ville, $dateEmbauche, $idOrigine]);

}


//=========================================================================
// Modifie un utilisateur sans changer son mot de passe
// Requete d'origine :
// UPDATE {table} SET id=?, nom=?, prenom=?, login=?, adresse=?, cp=?, ville=?, dateEmbauche=? WHERE id=?
//=========================================================================
public function modifierUtilisateurSansMdp($table, $id, $nom, $prenom, $login, $adresse, $cp, $ville, $dateEmbauche, $idOrigine) {

	$db = db_connect();

	$sql = "UPDATE {$table} SET id=?, nom=?, prenom=?, login=?, adresse=?, cp=?, ville=?, dateEmbauche=? WHERE id=?";

	return $db->query($sql, [$id, $nom, $prenom, $login, $adresse, $cp, $ville, $dateEmbauche, $idOrigine]);

}


//=========================================================================
// Supprime les lignes de frais forfaitaires liées à un visiteur
// Requete d'origine : DELETE FROM LigneFraisForfait WHERE idVisiteur = ?
//=========================================================================
public function supprimerLignesFraisForfait($idVisiteur) {

	$db = db_connect();

	$sql = 'DELETE FROM LigneFraisForfait WHERE idVisiteur = ?';

	return $db->query($sql, [$idVisiteur]);

}


//=========================================================================
// Supprime les lignes de frais hors forfait liées à un visiteur
// Requete d'origine : DELETE FROM LigneFraisHorsForfait WHERE idVisiteur = ?
//=========================================================================
public function supprimerLignesFraisHorsForfait($idVisiteur) {

	$db = db_connect();

	$sql = 'DELETE FROM LigneFraisHorsForfait WHERE idVisiteur = ?';

	return $db->query($sql, [$idVisiteur]);

}


//=========================================================================
// Supprime les fiches de frais liées à un visiteur
// Requete d'origine : DELETE FROM FicheFrais WHERE idVisiteur = ?
//=========================================================================
public function supprimerFicheFraisParVisiteur($idVisiteur) {

	$db = db_connect();

	$sql = 'DELETE FROM FicheFrais WHERE idVisiteur = ?';

	return $db->query($sql, [$idVisiteur]);

}


//=========================================================================
// Supprime un utilisateur (visiteur, comptable ou administrateur)
// Requete d'origine : DELETE FROM {table} WHERE id = ?
//=========================================================================
public function supprimerUtilisateur($table, $id) {

	$db = db_connect();

	$sql = "DELETE FROM {$table} WHERE id = ?";

	return $db->query($sql, [$id]);

}


//=========================================================================
// Récupère un utilisateur par son id, pour préremplir le formulaire d'édition
// Requete d'origine : SELECT * FROM {table} WHERE id = ?
//=========================================================================
public function getUtilisateurParId($table, $id) {

	$db = db_connect();

	$sql = "SELECT * FROM {$table} WHERE id = ?";

	$resultat = $db->query($sql, [$id]);

	return $resultat->getRowArray();

}


//=========================================================================
// Récupère la liste des utilisateurs d'un type donné, triée par nom/prénom
// Requete d'origine :
// SELECT id, nom, prenom, login, adresse, cp, ville, dateEmbauche FROM {table} ORDER BY nom, prenom
//=========================================================================
public function getUtilisateursParType($table) {

	$db = db_connect();

	$sql = "SELECT id, nom, prenom, login, adresse, cp, ville, dateEmbauche FROM {$table} ORDER BY nom, prenom";

	$resultat = $db->query($sql);

	return $resultat->getResultArray();

}


//=========================================================================
// ---- frais.php : consultation des frais d'un visiteur pour un mois donné ----
//=========================================================================

//=========================================================================
// Récupère les lignes de frais forfaitaires d'un visiteur pour un mois
// Requete d'origine :
// SELECT f.idFraisForfait, ff.libelle, ff.montant, f.quantite, (ff.montant * f.quantite) AS total
// FROM LigneFraisForfait AS f
// JOIN FraisForfait AS ff ON ff.id = f.idFraisForfait
// WHERE f.idVisiteur = ? AND f.mois = ?
//=========================================================================
public function getLignesFraisForfait($idVisiteur, $mois) {

	$db = db_connect();

	$sql = 'SELECT f.idFraisForfait, ff.libelle, ff.montant, f.quantite,
	                (ff.montant * f.quantite) AS total
	         FROM LigneFraisForfait AS f
	         JOIN FraisForfait AS ff ON ff.id = f.idFraisForfait
	         WHERE f.idVisiteur = ? AND f.mois = ?';

	$resultat = $db->query($sql, [$idVisiteur, $mois]);

	return $resultat->getResultArray();

}


//=========================================================================
// Récupère les lignes de frais hors forfait d'un visiteur pour un mois
// Requete d'origine :
// SELECT id, libelle, date, montant FROM LigneFraisHorsForfait WHERE idVisiteur = ? AND mois = ? ORDER BY date
//=========================================================================
public function getLignesFraisHorsForfait($idVisiteur, $mois) {

	$db = db_connect();

	$sql = 'SELECT id, libelle, date, montant
	         FROM LigneFraisHorsForfait
	         WHERE idVisiteur = ? AND mois = ?
	         ORDER BY date';

	$resultat = $db->query($sql, [$idVisiteur, $mois]);

	return $resultat->getResultArray();

}


//=========================================================================
// Récupère le libellé de l'état de la fiche de frais d'un visiteur pour un mois
// Requete d'origine :
// SELECT e.libelle AS etat FROM FicheFrais AS ff JOIN Etat AS e ON ff.idEtat = e.id
// WHERE ff.idVisiteur = ? AND ff.mois = ?
//=========================================================================
public function getEtatFicheFrais($idVisiteur, $mois) {

	$db = db_connect();

	$sql = 'SELECT e.libelle AS etat
	         FROM FicheFrais AS ff
	         JOIN Etat AS e ON ff.idEtat = e.id
	         WHERE ff.idVisiteur = ? AND ff.mois = ?';

	$resultat = $db->query($sql, [$idVisiteur, $mois]);

	return $resultat->getRowArray();

}


//=========================================================================
// ---- saisi.php : saisie des frais d'un visiteur ----
//=========================================================================

//=========================================================================
// Vérifie si une fiche de frais existe déjà pour un visiteur et un mois
// Requete d'origine : SELECT 1 FROM FicheFrais WHERE idVisiteur = ? AND mois = ?
//=========================================================================
public function ficheFraisExiste($idVisiteur, $mois) {

	$db = db_connect();

	$sql = 'SELECT 1 FROM FicheFrais WHERE idVisiteur = ? AND mois = ?';

	$resultat = $db->query($sql, [$idVisiteur, $mois]);

	return $resultat->getRowArray() !== null;

}


//=========================================================================
// Crée une fiche de frais pour un visiteur et un mois donnés
// Requete d'origine :
// INSERT INTO FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat)
// VALUES (?, ?, 0, 0.00, ?, ?)
//=========================================================================
public function creerFicheFrais($idVisiteur, $mois, $dateModif, $idEtat) {

	$db = db_connect();

	$sql = 'INSERT INTO FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, dateModif, idEtat)
	        VALUES (?, ?, 0, 0.00, ?, ?)';

	return $db->query($sql, [$idVisiteur, $mois, $dateModif, $idEtat]);

}


//=========================================================================
// Insère (ou ignore si déjà existante) une ligne de frais forfaitaire
// Requete d'origine :
// INSERT IGNORE INTO LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite) VALUES (?, ?, ?, ?)
//=========================================================================
public function insererLigneFraisForfait($idVisiteur, $mois, $idFraisForfait, $quantite) {

	$db = db_connect();

	$sql = 'INSERT IGNORE INTO LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite) VALUES (?, ?, ?, ?)';

	return $db->query($sql, [$idVisiteur, $mois, $idFraisForfait, $quantite]);

}


//=========================================================================
// Insère une ligne de frais hors forfait
// Requete d'origine :
// INSERT INTO LigneFraisHorsForfait (idVisiteur, mois, libelle, date, montant) VALUES (?, ?, ?, ?, ?)
//=========================================================================
public function insererLigneFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {

	$db = db_connect();

	$sql = 'INSERT INTO LigneFraisHorsForfait (idVisiteur, mois, libelle, date, montant) VALUES (?, ?, ?, ?, ?)';

	return $db->query($sql, [$idVisiteur, $mois, $libelle, $date, $montant]);

}


//==========================
// Fin Code du modele
//===========================


//fin de la classe
}
