<?php

// Initialise la connection avec la base de donnée
function connection()
{
	try {
		$dns = "mysql:host=mysql.info.unicaen.fr;port=3306;dbname=parchem211_dev";
		$user = "parchem211";
		$password = "Iitohh7avahd1sho";
		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);
		$connection = new PDO($dns, $user, $password, $options);
		return ($connection);
	} catch (Exception $e) {
		echo "Connection to MySQL impossible : " . $e->getMessage();
		die();
	}
}

// permet de stocker les produits afin d'avoir accès aux informations plus facilement
class Voiture
{
	public $id, $marque, $modele, $annee, $energie, $puissance, $puissance_fiscale, $gearbox, $type, $cylindree, $portes, $motricite;

	public function __construct($id, $marque, $modele, $annee, $energie, $puissance, $puissance_fiscale, $gearbox, $type, $cylindree, $portes, $motricite)
	{
		$this->id = $id;
		$this->marque = $marque;
		$this->modele = $modele;
		$this->annee = $annee;
		$this->energie = $energie;
		$this->puissance = $puissance;
		$this->puissance_fiscale = $puissance_fiscale;
		$this->gearbox = $gearbox;
		$this->type = $type;
		$this->cylindree = $cylindree;
		$this->portes = $portes;
		$this->motricite = $motricite;
	}

	public function __toString() {
		return "{$this->marque} {$this->modele} {$this->annee}";
	}
	
	public function affiche_simple() {
		return "
		<div class='voiture'>
			<div class='info_voiture'>
				<span class='marque_voiture'>{$this->marque}</span>
				<span class='modele_voiture'>{$this->modele}</span>
				<span class='annee_voiture'>{$this->annee}</span>
			</div>
			<div class='voiture_actions'>
				<a href='index.php?action=view&id={$this->id}'><img src='src/assets/icons/eye.svg' alt='afficher la voiture' class='view-icon'></a>
				<a href='index.php?action=edit&id={$this->id}'><img src='src/assets/icons/edit.svg' alt='modifier la voiture' class='edit-icon'></a>
				<a href='index.php?action=delete&id={$this->id}'><img src='src/assets/icons/delete.svg' alt='supprimer la voiture' class='delete-icon'></a>
			</div>
		</div>";
	}

	public function affiche_complet() {
		return <<<EOT
				<div class="details">
					<p><span>Marque :</span> {$this->marque}<p>	
					<p><span>Modèle :</span> {$this->modele}<p>
					<p><span>Année :</span> {$this->annee}<p>
					<p><span>Energie :</span> {$this->energie}<p>	
					<p><span>Puissance :</span> {$this->puissance} ch<p>	
					<p><span>Puissance fiscale :</span> {$this->puissance_fiscale} CV<p>	
					<p><span>Type :</span> {$this->type}<p>	
					<p><span>Boîte de vitesse :</span> {$this->gearbox}<p>	
					<p><span>Portes :</span> {$this->portes}<p>	
					<p><span>Cylindrée :</span> {$this->cylindree} cm<sup>3</sup><p>	
					<p><span>Motricité :</span> {$this->motricite}<p>	
				</div>
				EOT;
	}
}

// variables produit
$id = $marque = $modele = $annee = $energie = $puissance = $puissance_fiscale = $gearbox = $type = $cylindree = $portes = $motricite = null;
$erreurs = array("marque" => "", "modele" => "", "annee" => "", "energie" => "","puissance" => "", "puissance_fiscale" => "", "gearbox" => "", "type" => "", "cylindree" => "",
				"portes" => "", "motricite" => ""); //erreurs
$products = null; $filter = null; $sort = null;

