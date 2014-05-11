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
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
require ("config/confbd.inc.php");
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
  <script language="javascript">
function verif (formulaire)
{
var erreur = '';
if (formulaire.nom.value =='')
	{
	erreur = "Vous avez oublié d\'indiquer votre nom.\n";
	formulaire.nom.focus ();
	}
if (formulaire.prenom.value =='')
	{
	erreur = erreur+"Vous avez oublié d\'indiquer votre prénom.\n";
	formulaire.prenom.focus();
	}
if (!(formulaire.nouveau1.value == formulaire.nouveau2.value))
	{
	erreur = erreur+"Les deux versions de votre mot de passe ne correspondent pas.\n";
	formulaire.nouveau1.focus();
	formulaire.nouveau1.select();
	}
if ((formulaire.nouveau1.value == '') || (formulaire.nouveau2.value ==''))
	{
	erreur = erreur+"Vous devez indiquer deux fois votre nouveau mot de passe.\n";
	formulaire.nouveau1.focus();
	formulaire.nouveau1.select();
	}
if (erreur == '')
	return true;
	else
	{
	alert (erreur);
	return false;
	}
}
</script>
<script language="javascript" type="text/javascript" src="inc/fonctions.js"></script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup -->
  </script> 
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser(); // tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Modification du mot de passe et des données personnelles</h2>
<?php
$confirmation = isset($_POST['confirmation'])?$_POST['confirmation']:Null;

switch ($confirmation)
{
case 'Confirmer':
	$nom = $_POST["nom"];
	$prenom = $_POST["prenom"];
	$email = $_POST["email"];
	$mdp = md5($_POST["nouveau1"]);

	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);

	$_SESSION["identification"]["nom"]=mysql_real_escape_string($nom);
	$_SESSION["identification"]["prenom"]=mysql_real_escape_string($prenom);
	$_SESSION["identification"]["email"]=mysql_real_escape_string($email);
	$user = $_SESSION["identification"]["user"];
	
	$sql = "UPDATE ades_users SET nom='$nom', prenom='$prenom', email='$email', ";
	$sql .= "mdp='$mdp' WHERE user='$user'";
	
	// ---------------------------------------------------------------------------------
	// Mise à jour désactivée pour la démo
	$resultat = mysql_query ($sql);
	// ---------------------------------------------------------------------------------	
	mysql_close ($lienDB);
	redir ("index.php", "", "Modification du mot de passe de $nom effectuée");	
	break;
default:
	$user = $_SESSION["identification"]["user"];
	$nom = $_SESSION["identification"]["nom"];
	$prenom = $_SESSION["identification"]["prenom"];
	$email = $_SESSION["identification"]["email"];
	echo "<form name=\"form1\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" ";
	echo "onSubmit=\"return(verif(this))\">\n";
	echo "<p><label for=\"user\">Identifiant :</label>$user\n";
	echo "<input name=\"user\" type=\"hidden\" value= \"$user\" id=\"$user\"></p>\n";
	
	echo "<p><label for=\"nom\">Nom :</label>";
	echo "<input name=\"nom\" type=\"text\" value=\"$nom\" id=\"nom\"></p>\n";
	echo "<p><label for=\"prenom\">Prénom :</label>";
	echo "<input name=\"prenom\" type=\"text\" value=\"$prenom\" id=\"prenom\"></p>\n";
	echo "<p><label for=\"email\">e-mail : </label>";
	echo "<input name=\"email\" type=\"text\" value=\"$email\" id=\"email\"></p>\n";
	echo "<p><label for= \"nouveau1\">Mot de passe :</label>";
	echo "<input name=\"nouveau1\" type=\"password\" id=\"nouveau1\" ";
	echo "size=\"12\" maxlength=\"12\"></p>\n";
	echo "<p><label for=\"nouveau2\">Confirmation :</label>";
	echo "<input name=\"nouveau2\" type=\"password\" id=\"nouveau2\" ";
	echo "size=\"12\" maxlength=\"12\"></p>\n";	

	echo "<p style=\"text-align:center\"><input type=\"submit\" ";
	echo "name=\"confirmation\" value=\"Confirmer\">\n";
	echo "<input name=\"annulation\" value=\"Annuler\" type=\"reset\">\n</p>\n";

	echo "</form>\n";

	//----------------------------------------------
	// Avertissement pour la démo
	// echo "<script language='javascript'>alert('Dans la version de démonstration, les données de l\'utilisateur ne sont pas modifiées.')</script>\n";
	//----------------------------------------------	
	break;
}
echo retour();
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
