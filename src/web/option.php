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
 * Module d'option:
 * C'est à partir de cet page que l'administrateur va accéder aux différentes parties
 * du menu de configuration d'ADES 
 * 
 */
 //On inclut les librairies de fonctions de constantes et privé
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
</head>
<body>
<?php
// autorisations pour la page
autoriser ("admin");
// importation du menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Configuration ADES</h2>
<br>
<a href="confignomecole.php">Changer le nom de l'&eacute;cole</a>
<br>
<br>
<a href="configurationbilletretenue.php">Configurer le billet de retenue</a>
<br>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
