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
require ("config/constantes.inc.php");
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
  <script language="javascript" type="text/javascript" src="inc/fonctions.js"></script>
<script language="javascript" type="text/javascript">
function verifMDP (mdp1, mdp2)
{
erreur = (mdp1 != mdp2);
if (erreur)
	alert ('Les deux versions du mot de passe ne sont pas identiques');
return !erreur;
}

function verif(formulaire)
{
var verif1 = verifForm(formulaire);
var verif2 = verifMDP (formulaire.mdp1.value, formulaire.mdp2.value);

return (verif1 && verif2);
}
</script>
</head>
<body>
<?php
// autorisations pour la page
autoriser ("admin");
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Ajout/modification d'un utilisateur</h2>
<?php
// tester les différents modes d'entrée dans la page
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:Null;
switch ($mode)
	{
	case 'delete':
	// suppression d'un utilisateur
	$idedu = $_GET['idedu'];
	if (isset($_GET['confirme']))
		{
		include ("config/confbd.inc.php");
		$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
		mysql_select_db ($sql_bdd);
		$sql = "DELETE FROM ades_users WHERE idedu='$idedu'";
		$resultat = mysql_query ($sql);
		$nom=htmlspecialchars(stripslashes($_POST['nom']));
		$prenom = htmlspecialchars(stripslashes($_POST['prenom']));
		mysql_close ($lienDB);

		redir ($_SERVER['PHP_SELF'], "", 
			"Suppression de l'utilisateur <b>$prenom $nom</b> effectuée.");
		}
		else
		{
		include ("config/confbd.inc.php");
		$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
		mysql_select_db ($sql_bdd);
		$sql = "SELECT idedu, user, nom, prenom, email, mdp, privilege, timeover ";
		$sql .= "FROM ades_users WHERE idedu = $idedu";	
		// echo "$sql <br />";
		$resultat = mysql_query ($sql);
		mysql_close ($lienDB);
		while ($ligne = mysql_fetch_array($resultat))
			{
			$idedu = $ligne['idedu'];
			$user = $ligne['user'];
			$user = stripslashes($user);
			$nom = $ligne['nom'];
			$nom = stripslashes($nom)	;
			$prenom = $ligne['prenom'];
			$prenom = stripslashes($prenom);
			}	
		echo "<h3>Confirmez la suppression de l'utilisateur suivant</h3>\n";
		echo "<p><label>Nom :</label><b>$nom</b></p>\n";
		echo "<p><label>Prénom :</label><b>$prenom</b></p>\n";
		echo "<p style=\"text-align:center\"><img src=\"images/exclam.gif\" ";
		echo "height=\"32\" width=\"32\">";
		echo "<a href=\"{$_SERVER['PHP_SELF']}?mode=delete&idedu=$idedu&confirme=O\">";
		echo "Confirmer la suppression</a>";
		echo "<img src=\"images/exclam.gif\" height=\"32\" width=\"32\"></p>\n";
		echo "<p style=\"text-align:center\"><a href=\"".$_SERVER['PHP_SELF']."\">";
		echo "Annuler cette suppression</a></p>\n";
		}
	break;
	
	case 'Enregistrer':
	// enregistrement dans la BD
	include ("config/confbd.inc.php");
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);
	$np = $_POST['prenom']. " ".$_POST['nom'];
	
	$idedu = (isset($_POST['idedu'])) ? $_POST['idedu']: Null;
	$user = mysql_real_escape_string($_POST['user']);
	$nom = mysql_real_escape_string($_POST['nom']);
	$prenom = mysql_real_escape_string($_POST['prenom']);
	$email = mysql_real_escape_string($_POST['email']);
	$mdp = mysql_real_escape_string($_POST['mdp1']);
	$mdp = md5($mdp);
	$privilege = $_POST['privilege'];
	$timeover = $_POST['timeover'];

	$milieu = "SET user = '$user', nom='$nom', prenom = '$prenom', ";
	$milieu .= "email='$email', mdp='$mdp', privilege='$privilege',";
	$milieu .= "timeover = '$timeover'";
	if ($idedu != "")
		$sql = "UPDATE ades_users $milieu WHERE idedu = '$idedu'";
		else
		$sql = "INSERT INTO ades_users $milieu";
	// echo $sql;
	
	// echo "<script language=\"javascript\">";
	// echo "alert('Dans la version de démonstration, les données ne sont pas modifiées')";
	// echo "</script>";
	$resultat = mysql_query ($sql);
	$n = mysql_affected_rows();
	mysql_close ($lienDB);

	echo "<div style=\"text-align: center\">";
	if ($n==1)
	// une et une seule fiche a été enregistrée. Tout va bien
		redir ($_SERVER['PHP_SELF'],"","Enregistrement de la fiche de<br /> $np effectué.",1000);
		else
	// l'enregistrement s'est mal passé: pas de modification de la fiche ou doublon possible
		{
		$texte = "Enregistrement de la fiche de $np non effectué.\n";
		$texte .= "Veuillez  vérifier que la fiche a bien été modifiée ou que l'utilisateur n'existe pas déjà.";
		redir ($_SERVER['PHP_SELF'],"",$texte,5000);
		}
	echo "</div>\n";
	break;
	
	case 'editer':
	// édition d'une fiche: on récupère les données
	$idedu = $_GET['idedu'];
	include ("config/confbd.inc.php");
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);
	$sql = "SELECT idedu, user, nom, prenom, email, mdp, privilege, timeover ";
	$sql .= "FROM ades_users WHERE `idedu` = $idedu";	
	// echo "$sql <br />";
	$resultat = mysql_query ($sql);
	mysql_close ($lienDB);
	while ($ligne = mysql_fetch_assoc($resultat))
		{
		$idedu = $ligne['idedu'];
		$user = htmlspecialchars(stripslashes($ligne['user']));
		$nom = htmlspecialchars(stripslashes($ligne['nom']));
		$prenom = htmlspecialchars(stripslashes($ligne['prenom']));
		$email = htmlspecialchars(stripslashes($ligne['email']));
		$mdp = "";
		$privilege = htmlspecialchars(stripslashes($ligne['privilege']));
		$timeover = $ligne['timeover'];
		}
	// pas de break, on continue sur le formulaire
	
	case 'nouveau':
	// présentation du formulaire 
	require ("inc/utilisateur/formutilisateur.inc.php");
	break;
	
	default:
	// on présente les différents utilisateurs
	include ("config/confbd.inc.php");
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);
	$sql = "SELECT * FROM ades_users";	
	
	$resultat = mysql_query ($sql);
	mysql_close ($lienDB);
	
	while ($ligne = mysql_fetch_assoc($resultat))
		{
		$idedu = $ligne['idedu'];
		$nom = htmlspecialchars(stripslashes($ligne['nom']));
		$prenom = htmlspecialchars(stripslashes($ligne['prenom']));
		$user = htmlspecialchars(stripslashes($ligne['user']));
		echo "<a href=\"".$_SERVER['PHP_SELF']."?mode=editer&idedu=$idedu\">";
		echo "<img src=\"images/editer.png\" width=\"16\" height=\"16\" ";
		echo "border=\"0\" alt=\"edit\" title=\"Modifier\"></a>\n";
		if ($idedu!=1)
			{
			echo "<a href=\"".$_SERVER['PHP_SELF']."?mode=delete&idedu=$idedu\">";
			echo "<img src=\"images/suppr.png\" width=\"16\" height=\"16\" ";
			echo "border=\"0\" alt=\"suppr\" title=\"Supprimer\"></a>\n";
			}
		echo "$nom $prenom ($user)<br />\n";
		}
	echo "<p><a href=\"{$_SERVER['PHP_SELF']}?mode=nouveau\">Ajouter un utilisateur</a>\n";
	break;
	}	
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
