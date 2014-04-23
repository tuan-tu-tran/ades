<?php 
/**
 * Copyright (c) 2014 Educ-Action
 * 
 * This file is part of ADES.
 * 
 * ADES is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ADES is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with ADES.  If not, see <http://www.gnu.org/licenses/>.
*/
include ("inc/prive.inc.php");
include ("inc/fonctions.inc.php");
include ("config/constantes.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
  <link media="print" rel="stylesheet" href="config/print.css" type="text/css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
<script language="javascript" type="text/javascript" src="inc/fonctions.js">
</script>
<script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser ("admin");
// menu
require ("inc/menu.inc.php");

//Rami Adrien
//La fonction afin de déterminer le dernier enregistrement a été déplacé de l'accueil pour arriver dans le menu de backup de l'application
if (utilisateurParmi ("educ", "admin"))
	{
	$display = "block";
	$dernier = dernierEnregistrement("./sauvegarde");
	if ($dernier == "")
		$sauvegarde ="Aucun";
		else
		{
		$dateSauvegarde = filemtime ("./sauvegarde/$dernier");
		$ceJour = time();
		$nbjours = floor((time() - $dateSauvegarde)/86400);
		}
	}
	else $display = "none";
//Affichage dans la page du dernier enregistrements
?>
<fieldset id="cadreGauche" style="display:##DISPLAY##">
<legend>Sécurité</legend>
<p class="impt">La dernière sauvegarde <?php echo $dernier?> date de <?php echo $nbjours?> jours.</p>
</fieldset>
<div id="texte">
<h2>Sauvegardes de la base de données</h2>
<?php
$mode = isset($_GET['mode'])?$_GET['mode']:Null;
$fichier = isset($_GET['fichier'])?$_GET['fichier']:Null;

switch ($mode)
{
case 'suppr':
	echo tableauSauvegardes ($fichier);
	break;
default:
	include ("config/confbd.inc.php");
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);

	// Création du fichier de sauvegarde
	$fichiersql = "./sauvegarde/".date('Ymd').".sql";
	// on ouvre le fichier .sql en écriture et on le crée s'il est inexistant
	$sauve = fopen($fichiersql , "w");

	// liste des tables à sauvegarder
	$tables = array("ades_users", "ades_faits", "ades_eleves", "ades_retenues");

	foreach ($tables as $uneTable)
		{
		$titre = "-- \n";
		$titre .= "-- Structure de la table `$uneTable` \n";
		$titre .= "-- \n \n ";
		fputs($sauve, $titre);
	
		// instructions de création de la table
		$sql = "SHOW CREATE TABLE $uneTable ";
		$resultat = mysql_query($sql) 
			or die ("Impossible de trouver la structure de ". $uneTable .mysql_error());
		$donnee_structure = mysql_fetch_array($resultat);
		$structure = $donnee_structure[1] .";\n \n";
		fputs($sauve, $structure);		

		// compter le nombre de champs présents dans la table
		$sql = "SHOW COLUMNS FROM $uneTable";
		$resultat = mysql_query ($sql) 
			or die ("Impossible de trouver les champs de ". $uneTable .mysql_error());
		$nbre_champ = mysql_num_rows($resultat);

		// on recherche tous les enregistrements de la table concernée
		$sql = "SELECT * FROM $uneTable ";
		$resultat = mysql_query($sql) 
		or die ("Impossible de trouver les enregistrements de ". $uneTable .mysql_error());

		// on boucle pour sortir toutes les données
		while ($donnees = mysql_fetch_array($resultat))
			{
			$contenu = "INSERT INTO $uneTable VALUES (";
			$i = 0;
			// on boucle sur tous les champs
			while ( $i < $nbre_champ )
				{
				// On sremplace les apostrophes du contenu par 2 apostrophes
				$donnees[$i] = htmlentities($donnees[$i]);
				$donnees[$i] = str_replace("'","''",$donnees[$i]);
				// $donnees[$i] = nl2br($donnees[$i]);
				// On stocke les résultats en fonction des champs et dans l'ordre des champs
				$contenu .= "'" . $donnees[$i] . "',";
				$i++;
				}
			// on enlève la dernière virgule
			$contenu = substr($contenu,0,-1);
			$contenu .= ");\n";
			fputs($sauve, $contenu);
			}
		}
	// on ferme le fichier de sauvegarde
	fclose($sauve);
	// on ferme sa connexion au serveur
	mysql_close();

	echo tableauSauvegardes ();
	break;
}
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
