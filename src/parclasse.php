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
include ("config/confbd.inc.php");
require ("inc/fonctions.inc.php");
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
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page

// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Recherche de la fiche d'un élèveve</h2>
<h3>Classe de l'élève</h3>
<div id="memento" style="float:left">
<?php
// recherche de toutes les classes existantes dans le fichier
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "SELECT DISTINCT classe FROM ades_eleves";
$resultat = mysql_query ($sql);
mysql_close ($lienDB);

$tableauClasses = array();
while ($classes = mysql_fetch_assoc($resultat))
	{
	$laClasse = ($classes['classe']);
	$car1 = substr($laClasse,0,1);
	$tableauClasses[$car1][] = $laClasse;
	}

$liste = "";
foreach ($tableauClasses as $unNiveau)
	{
	$liste .= "<ul>\n";
	foreach ($unNiveau as $uneClasse)
		$liste .= "\t<li><a href=\"nomeleve.php?classe=$uneClasse\" title=\"$uneClasse\">$uneClasse</a></li>\n";
	$liste .= "</ul>\n";
	}
echo $liste;
?>
</div>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
