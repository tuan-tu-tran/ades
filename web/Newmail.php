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
/* Rami Adrien
 * Module d'option/ Configuration du billet de retenue:
 * C'est à partir de ce menu que l'administrateur va pouvoir configurer le billet de retenue à sa guise
 * 
 */
 //On inclut les librairies de fonctions de constantes et priv�
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
<link media="print" rel="stylesheet" href="config/print.css" type="text/css">
<link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
</script>
</head>
<body>
<?php
// autorisations pour la page
autoriser ();
// importation du menu
require ("inc/menu.inc.php");

$ListeUserSql = "SELECT idedu, user, nom, prenom FROM ades_users";
$ReqListeUserSql = mysql_query($ListeUserSql);
?>
<div id="texte">
<div id="Destinataire">
<form name="form" method="post" action="Sendmail.php">
<b>Destinataire:</b>
<table border ="1">
<?php
while ($LigneInfo = mysql_fetch_assoc($ReqListeUserSql))
	{
	echo "<tr>";
	echo "<td>";
	echo $LigneInfo['nom']." ".$LigneInfo['prenom'];
	echo "</td>";
	echo "<td><input name=\"destinataire[]\" type=\"checkbox\" id=\"destinataire[]\" value=".$LigneInfo['idedu']."></td></br>";
	echo "</tr>";
	}
?>
</table>
</div></br>
<div id="NewMail">
<b>Objet:</b>
<input type="text" id="Sujet" name="Sujet" size="50"></br></br>
<textarea id="Corps" name="Corps" cols="80" rows = "15"></textarea></br></br>
<input name="Submit" value="Envoyer" type="submit"></br></br>
</div>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
