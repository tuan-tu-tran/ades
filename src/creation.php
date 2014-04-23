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
include ("inc/fonctions.inc.php");
include ("config/constantes.inc.php");
Normalisation();
// On inclut plus automatiquement le fichier de configuration on l'inclut uniquement si il existe ce qui permet de s'adapter au processus de création du fichier
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Initialisation de la base de données ADES</title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
<?php
function CreationTables ()
{
$commandes = file("./creation.sql");
$uneCommande = "";
$nbEtoiles = 0;
foreach ($commandes as $uneLigne)
	{
	// supprimer les commentaires dans le fichier .sql
	if (substr($uneCommande, 0, 2) == "--")
		$uneCommande = "";
	$uneCommande .= trim($uneLigne);
	$longueur = strlen($uneCommande);
	$dernier = substr($uneCommande, $longueur-1, 1);
	if ($dernier == ";")
		{
		$resultat = mysql_query($uneCommande);
		$nb = mysql_affected_rows ();
		$uneCommande = "";
		}
	}
}
?>
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
switch ($etape)
	{
	case 0: 
		echo("<p>Cet utilitaire va:</p>\n");
		echo("<ul>\n<li>+ créer votre fichier de configuration</li>\n");
		echo("<li>+ créer les tables de la base de données</li>\n");
		echo("</ul>\n");
		echo("Avant de commencer veuillez préparer les informations suivantes:</p>\n");
		echo("<ul>\n<li>+ votre serveur sql</li>\n");
		echo("<li>+ l'utilisateur sql</li>\n");
		echo("<li>+ le mot de passe</li>\n");
		echo("<li>+ le nom de la base de données</li>\n");
		echo("</ul>\n");
		echo("<p><big>ETES VOUS SÛR(E) DE VOULOIR POURSUIVRE?</big></p>\n");
		$etape++;
		echo ("<p><a href=\"creation.php?etape=$etape\">Oui, je sais ce que je fais</a></p>\n");
		break;
	case 1: 
		echo("<form name=\"form\" method=\"post\" action=\"creation.php\">");
		echo("<p><label>Serveur Sql :</label><input name=\"sqlserver\" id=\"sqlserver\" size=\"30\" maxlength=\"50\" type=\"text\"></p>");
		echo("<p><label>Utilisateur :</label><input name=\"utilisateursql\" id=\"utilisateur\" size=\"30\" maxlength=\"50\" type=\"text\"></p>");
		echo("<p><label>Mot de Passe :</label><input name=\"motdepassesql\" id=\"motdepasse\" size=\"30\" maxlength=\"50\" type=\"password\"></p>");
		echo("<p><label>Nom de la Base de données :</label><input name=\"nomdelabasesql\" id=\"nomdelabase\" size=\"30\" maxlength=\"50\" type=\"text\"></p>");
		echo("<input name=\"Submit\" value=\"Enregistrer\" type=\"submit\">");
		$etape++;
		break;
	case 2:
		// Rami Adrien création du fichier confdb.inc.php
		$fichierconfdb = fopen("config/confbd.inc.php","w");
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
		$etape++;
		echo("Fichier de configuration créer avec succès<br>");
		echo("<a href=\"creation.php?etape=$etape\">Installation d'ADES</a>\n");
		break;
	case 3: ;
		if(file_exists("config/confbd.inc.php")){
			//Si le fichier existe on l'inclut dans le programme et l'interface se charge pour l'ajout des tables
			include ("config/confbd.inc.php");
			$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd) or die(mysql_error());
			mysql_select_db($sql_bdd);
			creationTables ($sql_bdd);
		}
		echo("<p>Login et mot de passe: admin</p>");
		echo("<p>L'installation d'ADES est terminé: <a href=\"index.php\">On y va</a></p>");
		$etape++;
		if(file_exists("config/confbd.inc.php")){
			mysql_close ($lienDB);
		}
		break;
	default: echo("L'installation d'ADES est un succès");
	}

?>
</div>
</body>
</html>
