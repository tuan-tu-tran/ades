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
/* Ades : Creation.php version 2
 * Rami Adrien: rami@adrien.be
 * Modification:
 * Supression de la suppression et de la création de la table
 * Création automatique du fichier de configuration
 * Tout le processus de configuration d'ADES est automatisé
 * Au lancement de l'application ADES détecte si ADES est configuré
 */
require("inc/init.inc.php");
Normalisation();
function CreationTables (){
	$commandes = file("./creation.sql");
	$uneCommande = "";
	$nbEtoiles = 0;
	foreach ($commandes as $uneLigne){
		// supprimer les commentaires dans le fichier .sql
		if (substr($uneCommande, 0, 2) == "--")
			$uneCommande = "";
		$uneCommande .= trim($uneLigne);
		$longueur = strlen($uneCommande);
		$dernier = substr($uneCommande, $longueur-1, 1);
		if ($dernier == ";"){
			$resultat = mysql_query($uneCommande);
			$nb = mysql_affected_rows ();
			$uneCommande = "";
		}
	}
}

function CreateConfigFile(){
	// Rami Adrien création du fichier confdb.inc.php
	$fichierconfdb = fopen(_DB_CONFIG_FILE_,"w");
	fwrite($fichierconfdb, "<?php \n");
	fwrite($fichierconfdb, "// SERVEUR SQL");
	fwrite($fichierconfdb, "\n");
	fwrite($fichierconfdb, '$sql_serveur="');
	fwrite($fichierconfdb, $_POST['sqlserver']);
	fwrite($fichierconfdb, "\";\n");
	fwrite($fichierconfdb, "// LOGIN SQL");
	fwrite($fichierconfdb, "\n");
	fwrite($fichierconfdb, '$sql_user="');
	fwrite($fichierconfdb,$_POST['utilisateursql']);
	fwrite($fichierconfdb, "\";\n");
	fwrite($fichierconfdb, "// MOT DE PASSE SQL");
	fwrite($fichierconfdb, "\n");
	fwrite($fichierconfdb, '$sql_passwd="');
	fwrite($fichierconfdb, $_POST['motdepassesql']);
	fwrite($fichierconfdb, "\";\n");
	fwrite($fichierconfdb, "// NOM DE LA BASE DE DONNEES");
	fwrite($fichierconfdb, "\n");
	fwrite($fichierconfdb, '$sql_bdd="');
	fwrite($fichierconfdb,$_POST['nomdelabasesql']);
	fwrite($fichierconfdb, "\";\n");
	fwrite($fichierconfdb, '$sql_prefix=""');
	fwrite($fichierconfdb, ";\n");
	fwrite($fichierconfdb, "?>");
	fclose($fichierconfdb);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Initialisation de la base de données ADES</title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
</head>
<body>
<div id="texte">
<h2>Installation d'ADES</h2>
<?php
/* Rami Adrien: Installation automatique:
 * J'ai rajouté dans le ce fichier une étape en plus à savoir la création du fichier de 
 * configuration
 *
 */
$etape = isset($_GET['etape'])?$_GET['etape']:0;
// Détection pour savoir si l'on se trouve à l'étape de la création du fichier de configuration
if(empty($_POST['sqlserver'])==false){
	$etape= 2;
}
?>
<?php if($etape==0):?>

	<p>Cet utilitaire va:</p>
	<ul>
		<li>+ créer votre fichier de configuration</li>
		<li>+ créer les tables de la base de données</li>
	</ul>

	<p>Avant de commencer veuillez préparer les informations suivantes:</p>
	<ul>
		<li>+ votre serveur sql</li>
		<li>+ l'utilisateur sql</li>
		<li>+ le mot de passe</li>
		<li>+ le nom de la base de données</li>
	</ul>

	<p><big>ETES VOUS SÛR(E) DE VOULOIR POURSUIVRE?</big></p>

	<p><a href="creation.php?etape=1">Oui, je sais ce que je fais</a></p>

<?php elseif($etape==1):?>

	<form name="form" method="post" action="creation.php">
		<p><label>Serveur Sql :</label><input name="sqlserver" id="sqlserver" size="30" maxlength="50" type="text"></p>
		<p><label>Utilisateur :</label><input name="utilisateursql" id="utilisateur" size="30" maxlength="50" type="text"></p>
		<p><label>Mot de Passe :</label><input name="motdepassesql" id="motdepasse" size="30" maxlength="50" type="password"></p>
		<p><label>Nom de la Base de données :</label><input name="nomdelabasesql" id="nomdelabase" size="30" maxlength="50" type="text"></p>
		<input name="Submit" value="Enregistrer" type="submit">
	</form>

<?php elseif($etape==2):?>

	<?php
		//create the config file
		CreateConfigFile()
	?>
	<p>Fichier de configuration créer avec succès</p>
	<a href="creation.php?etape=3">Installation d'ADES</a>

<?php elseif($etape==3):?>

	<?php
		//create the tables 
		if(file_exists(_DB_CONFIG_FILE_)){
			//Si le fichier existe on l'inclut dans le programme et l'interface se charge pour l'ajout des tables
			include(_DB_CONFIG_FILE_);
			$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd) or die(mysql_error());
			mysql_select_db($sql_bdd);
			CreationTables();
			mysql_close ($lienDB);
		}
	?>
		<p>Login et mot de passe: admin</p>
		<p>L'installation d'ADES est terminée: <a href="index.php">On y va</a></p>
<?php endif; ?>
</div>
</body>
</html>
