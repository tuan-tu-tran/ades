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
require ("inc/prive.inc.php");
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
require ("inc/classes/classSynthese.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link rel="stylesheet" href="config/screen.css" type="text/css" media="screen">
  <link rel="stylesheet" href="config/print.css" type="text/css" media="print">
  <link rel="stylesheet" type="text/css" href="config/calendrier.css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
<script language="javascript" type="text/javascript" src="cal/calendrier.js">
</script>
<script language="javascript" type="text/javascript" src="inc/fonctiondate.js">
</script>
<script language="javascript" type="text/javascript" src="inc/fonctions.js">
</script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup -->
  </script>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser();
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<b>ATTENTION CETTE OPERATION VA SUPPRIMER TOUTES LES DONNEES ELEVES AINSI QUE RETENUES ET FAITS.</br></br>

VOULEZ-VOUS CONTINUER?</br></br>
</br>
<a href="viderConfirmation.php">OUI</a>
</br>
</br>
<a href="index.php">NON</a>
</b>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
