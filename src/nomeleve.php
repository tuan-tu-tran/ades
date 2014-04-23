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
include ("inc/funcdate.inc.php");
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
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup -->
  </script> 
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser ();  // tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Sélection du nom de l'élève</h2>
<?php
$classe=(isset($_REQUEST['classe'])?$_REQUEST['classe']:Null);

include ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);

// Recherche des élèves qui figurent dans la classe $classe
$sql = "select ideleve, nom, prenom FROM ades_eleves ";
$sql .= "WHERE classe = '$classe' ORDER BY nom, prenom";
$listeEleves = @mysql_query($sql);

// on passe les élèves en revue
echo "<h3>$classe</h3>\n";
$n=1;
while ($eleve = mysql_fetch_array($listeEleves))
	{
	foreach ($eleve as $key=>$rubrique)
		$$key = $rubrique;
	echo "<a href=\"ficheel.php?mode=voir&amp;ideleve=$ideleve\">";
	echo "$n -> $nom $prenom</a><br>\n";
	$n++;
	}
echo retourIndex ();

mysql_close ($lienDB);
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
