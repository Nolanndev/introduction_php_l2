<?php

require_once("src/requires/util.php");

$action = key_exists('action', $_GET) ? trim($_GET["action"]) : null;

switch ($action) {
	case "about":
		$page_title = "iCars - about";
		$main_content = <<<EOT
			<div class="about">
				<div class="about_info">
					<p><span>Nom :</span> PARCHEMINER</p>
					<p><span>Prénom :</span> Nolann</p>
					<p><span>Numétu :</span> 22101157</p>
					<p><span>Groupe :</span> 4A</p>
				</div>
				<div class="description">
					<p>
						Ce site est le résultat du fil rouge à réaliser en technologies web 3, les voitures sont le thème de ce projet.
						Il y a la possibilité d'ajouter une voiture, d'avoir une page détaillée de chaque voiture, de modifier une voiture, ainsi que de les supprimer.
					</p>
					<span>Complément</span>
					<p>
						Possibilité de trier les voitures par marque, modèle et année (dans l'ordre croissant) en cliquant sur les entête dans sur la page principale
					</p>
					<span>Autre</span>
					<p>
						Les polices et les icones utilisées sont libres de droit et accessibles à tous.
					</p>
				</div>
			</div>
		EOT;
		break;

	case "list":
		$page_title = "iCars - Découvrir";
		$connection = connection();

		$sort = isset($_GET["sort"]) ? "ORDER BY " . $_GET["sort"] : "";
		$requete = "SELECT * FROM Voitures " . $sort;
		$query = $connection->query($requete);
		$query->setFetchMode(PDO::FETCH_OBJ);
		
		$voitures = array();
		while(($voiture = $query->fetch())) {
			array_push($voitures, new Voiture($voiture->id, $voiture->marque, $voiture->modele, $voiture->annee, $voiture->energie, $voiture->puissance, $voiture->puissance_fiscale, $voiture->gearbox, $voiture->type, $voiture->cylindree, $voiture->portes, $voiture->motricite));
		}
		
		$main_content = "";
		$main_content .= "<a href='index.php?action=insert' id='add_button'>Ajouter <img src='src/assets/icons/add.svg' alt='ajouter une voiture' id='add-icon'></a>";
		
		$main_content .= "<div class='list'>";
		$main_content .= <<<EOT
			<div class='voiture'>
				<div class='info_voiture'>
					<a href="index.php?action=list&sort=marque" class='marque'>Marque</a>
					<a href="index.php?action=list&sort=modele" class='modele'>Modèle</a>
					<a href="index.php?action=list&sort=annee" class='annee'>Année</a>
				</div>
				<div class='voiture_actions'>
					<span class='marque'>Voir</span>
					<span class='modele'>Modifier</span>
					<span class='annee'>Supprimer</span>
				</div>
			</div>
		EOT;
		foreach($voitures as $voiture) {
			$main_content .= $voiture->affiche_simple();
		}
		$main_content .= "</div>";
		$query = null;
		$connection = null;
		break;

	case "insert":
		$page_title = "iCars - Nouvelle voiture";
		$main_content = "";
		$cible = "insert";
		$submit_msg = "Ajouter";
		if (!isset($_POST["marque"]) && 
										!isset($_POST["modele"]) && 
										!isset($_POST["annee"]) && 
										!isset($_POST["energie"]) && 
										!isset($_POST["puissance"]) && 
										!isset($_POST["puissance_fiscale"]) && 
										!isset($_POST["gearbox"]) && 
										!isset($_POST["type"]) && 
										!isset($_POST["cylindree"]) && 
										!isset($_POST["portes"]) && 
										!isset($_POST["motricite"])) {
			include("src/requires/form.html");
		} else {
			$marque = key_exists('marque', $_POST) ? trim($_POST["marque"]) : null;
			$modele = key_exists('modele', $_POST) ? trim($_POST["modele"]) : null;
			$annee = key_exists('annee', $_POST) ? trim($_POST["annee"]) : null;
			$energie = key_exists('energie', $_POST) ? trim($_POST["energie"]) : null;
			$puissance = key_exists('puissance', $_POST) ? trim($_POST["puissance"]) : null;
			$puissance_fiscale = key_exists('puissance_fiscale', $_POST) ? trim($_POST["puissance_fiscale"]) : null;
			$gearbox = key_exists('gearbox', $_POST) ? trim($_POST["gearbox"]) : null;
			$type = key_exists('type', $_POST) ? trim($_POST["type"]) : null;
			$cylindree = key_exists('cylindree', $_POST) ? trim($_POST["cylindree"]) : null;
			$portes = key_exists('portes', $_POST) ? trim($_POST["portes"]) : null;
			$motricite = key_exists('motricite', $_POST) ? trim($_POST["motricite"]) : null;

			if ($marque == "") $erreurs["marque"] = "erreur";
			if ($modele == "") $erreurs["modele"] = "erreur";
			if ($annee == "") $erreurs["annee"] = "erreur";
			if ($energie == "") $erreurs["energie"] = "erreur";
			if ($puissance == "") $erreurs["puissance"] = "erreur";
			if ($puissance_fiscale == "") $erreurs["puissance_fiscale"] = "erreur";
			if ($gearbox == "") $erreurs["gearbox"] = "erreur";
			if ($type == "") $erreurs["type"] = "erreur";
			if ($cylindree == "") $erreurs["cylindree"] = "erreur";
			if ($portes == "") $erreurs["portes"] = "erreur";
			if ($motricite == "") $erreurs["motricite"] = "erreur";
			
			$compteur_erreurs = count($erreurs);
			foreach($erreurs as $cle => $valeur) {
				if ($valeur == null) $compteur_erreurs -= 1;
			}
			if ($compteur_erreurs == 0) {
				$connection = connection();
				$request = $connection->prepare("INSERT INTO Voitures (marque, modele, annee, energie, puissance, puissance_fiscale, gearbox, type, cylindree, portes, motricite) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
				$request->execute([$marque, $modele, $annee, $energie, $puissance, $puissance_fiscale, $gearbox, $type, $cylindree, $portes, $motricite]);
				$connection = null;
				header("Location:index.php?action=list");
			} else {
				include("src/requires/form.html");
			}
		}
		break;

	case "edit":
		$submit_msg = "Modifier";
		$main_content = "";
		if (isset($_GET["id"])) {
			$cible = "edit";
			$id = $_GET["id"];
			$connection = connection();
			$request = $connection->query("SELECT * FROM Voitures WHERE id = $id");
			$request->setFetchMode(PDO::FETCH_OBJ);
			$item = $request->fetch();

			$marque = $item->marque;
			$modele = $item->modele;
			$annee = $item->annee;
			$energie = $item->energie;
			$puissance = $item->puissance;
			$puissance_fiscale = $item->puissance_fiscale;
			$gearbox = $item->gearbox;
			$type = $item->type;
			$cylindree = $item->cylindree;
			$portes = $item->portes;
			$motricite = $item->motricite;
			
			include("src/requires/form.html");
			if (
				!isset($_POST["marque"]) ||
				!isset($_POST["modele"]) ||
				!isset($_POST["annee"]) ||
				!isset($_POST["energie"]) ||
				!isset($_POST["puissance"]) ||
				!isset($_POST["puissance_fiscale"]) ||
				!isset($_POST["gearbox"]) ||
				!isset($_POST["type"]) ||
				!isset($_POST["cylindree"]) ||
				!isset($_POST["portes"]) ||
				!isset($_POST["motricite"])
			) {
				include("src/requires/form.html");
			} else {
				$marque = key_exists('marque', $_POST) ? trim($_POST["marque"]) : null;
				$modele = key_exists('modele', $_POST) ? trim($_POST["modele"]) : null;
				$annee = key_exists('annee', $_POST) ? trim($_POST["annee"]) : null;
				$energie = key_exists('energie', $_POST) ? trim($_POST["energie"]) : null;
				$puissance = key_exists('puissance', $_POST) ? trim($_POST["puissance"]) : null;
				$puissance_fiscale = key_exists('puissance_fiscale', $_POST) ? trim($_POST["puissance_fiscale"]) : null;
				$gearbox = key_exists('gearbox', $_POST) ? trim($_POST["gearbox"]) : null;
				$type = key_exists('type', $_POST) ? trim($_POST["type"]) : null;
				$cylindree = key_exists('cylindree', $_POST) ? trim($_POST["cylindree"]) : null;
				$portes = key_exists('portes', $_POST) ? trim($_POST["portes"]) : null;
				$motricite = key_exists('motricite', $_POST) ? trim($_POST["motricite"]) : null;
	
				if ($marque == "") $erreurs["marque"] = "erreur";
				if ($modele == "") $erreurs["modele"] = "erreur";
				if ($annee == "") $erreurs["annee"] = "erreur";
				if ($energie == "") $erreurs["energie"] = "erreur";
				if ($puissance == "") $erreurs["puissance"] = "erreur";
				if ($puissance_fiscale == "") $erreurs["puissance_fiscale"] = "erreur";
				if ($gearbox == "") $erreurs["gearbox"] = "erreur";
				if ($type == "") $erreurs["type"] = "erreur";
				if ($cylindree == "") $erreurs["cylindree"] = "erreur";
				if ($portes == "") $erreurs["portes"] = "erreur";
				if ($motricite == "") $erreurs["motricite"] = "erreur";
				
				$compteur_erreurs = count($erreurs);
				foreach($erreurs as $cle => $valeur) {
					if ($valeur == null) $compteur_erreurs -= 1;
				}
				if ($compteur_erreurs == 0) {
					$connection = connection();
					$request = $connection->prepare("UPDATE Voitures SET marque=?, modele=?, annee=?, energie=?, puissance=?, puissance_fiscale=?, gearbox=?, type=?, cylindree=?, portes=?, motricite=?");
					$request->execute([$marque, $modele, $annee, $energie, $puissance, $puissance_fiscale, $gearbox, $type, $cylindree, $portes, $motricite]);
					$connection = null;
					header("Location:index.php?action=list");
				} else {
					include("src/requires/form.html");
				}
			}
		break;
	}

	case "delete":
		$page_title = "iCars - Suppression";
		$main_content = "";
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
			$connection = connection();
			$request = $connection->prepare("DELETE FROM Voitures WHERE id = ?");
			$request->execute([$id]);
			$connection = null;
			header("Location:index.php?action=list");
		}
		break;

	case "view":
		if (isset($_GET["id"])) {
			$id = $_GET["id"];
			$connection = connection();
			$request = $connection->query("SELECT * FROM Voitures WHERE id = $id");
			$request->setFetchMode(PDO::FETCH_OBJ);
			$obj = $request->fetch();
			$voiture = new Voiture($obj->id, $obj->marque, $obj->modele, $obj->annee, $obj->energie, $obj->puissance, $obj->puissance_fiscale, $obj->gearbox, $obj->type, $obj->cylindree, $obj->portes, $obj->motricite);
			$main_content = $voiture->affiche_complet();
			$page_title = "{$voiture->marque} {$voiture->modele} {$voiture->annee}";
		}
		break;

	default:
		$page_title = "iCars";
		$main_content = <<<EOT
			<div id="welcome_content">	
				<h2 id='welcome'>Bienvenue</h2>
				<a href='index.php?action=list' id='discover_link'>Découvrir</a>
			</div>
			EOT;
		break;
}


include("src/requires/skeleton.php");
