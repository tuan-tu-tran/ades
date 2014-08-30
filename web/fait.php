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
require ("inc/funcdate.inc.php");
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link rel="stylesheet" type="text/css" href="config/calendrier.css"> 
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
  <link media="print" rel="stylesheet" href="config/print.css" type="text/css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">  
  <script language="javascript" type="text/javascript" src="cal/calendrier.js"></script>
  <script language="javascript" type="text/javascript" src="inc/fonctions.js"></script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>  
    <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>  
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>  
    <script type="text/javascript">
    jQuery.noConflict();
    </script>  
</head>
<body>
<?php
// autorisations pour la page
autoriser ("educ","admin");
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<h2>Faits disciplinaires</h2>
<?php
$mode		= isset($_REQUEST['mode'])		? $_REQUEST['mode'] :	Null;
$type		= isset($_REQUEST['type'])		? $_REQUEST['type'] :	Null;
$ideleve	= isset($_REQUEST['ideleve'])	? $_REQUEST['ideleve']:	Null;
$idfait		= isset($_REQUEST['idfait'])	? $_REQUEST['idfait'] :	Null;
require ("inc/classes/classeleve.inc.php");
$eleve = new eleve($ideleve);
echo $eleve->shortnomprclasse();

require ("inc/classes/classfaits.inc.php");
switch ($mode)
	{
	case 'nouveau':
		// on vérifie que l'on dispose d'un $type et d'un $ideleve
		if ((isset($ideleve) && isset($type))) 
			{
			// c'est un nouveau fait que l'on initialise à l'idfait = -1
			$fait = new fait (-1, $type, $ideleve);
			// on présente le formulaire correspondant au fait
			echo $fait->formulaire($ideleve);
			}
		else jeter();
        break;
	case 'editer':  // on vérifie que l'on dispose d'un $idfait; les autres infos sont dans la BD
		autoriser ("educ", "admin");
		if (isset($idfait)) 
			{
			// initialiser le fait avec les valeurs trouvées dans la BD
			$fait = new fait($idfait);
			// on présente le formulaire correspondant au fait
			echo $fait->formulaire($ideleve);
			}
		else jeter();
		break;
	case 'confirmer': // demander la confirmation de la suppression
		// on vérifie que l'on dispose d'un $idfait 
		if (isset($idfait) && isset($ideleve))
			{
			$fait = new fait($idfait);
			echo $fait->confirmeSuppression($ideleve);
			}
		else jeter();
        break;
	case 'Supprimer':
		// on vérifie que l'on dispose d'un $idfait
		if (isset($idfait) && isset($ideleve))
			{
			$fait = new fait($idfait);
			$fait->supprimer($ideleve);
			}
		else jeter();
		break;
	case 'Enregistrer':
		// enregistrement du fait
		$fait = new fait();
		$fait->ramassePost($_POST);
		$fait->enregistrer();
		break;
		// dans tous les autres cas, on refuse l'accès à la page
	default:	
		jeter(); 
		break;
}
echo retour();
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
