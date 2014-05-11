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
require ("inc/funcdate.inc.php");
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
  <link media="screen" rel="stylesheet" href="config/retenues.css" type="text/css">  
  <link rel="stylesheet" type="text/css" href="heure/horloge.css" media="screen">
  <link rel="stylesheet" type="text/css" href="config/calendrier.css" media="screen">
  <link rel="stylesheet" type="text/css" href="config/menu.css" media="screen">
  <script language="javascript" type="text/javascript" src="cal/calendrier.js">
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
autoriser ("educ", "admin");
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Dates et modification des dates de retenues</h2>
<?php
// $mode = façon d'appeler la page
$mode           = isset($_REQUEST['mode'])	? $_REQUEST['mode'] :           Null;
$idretenue      = isset($_REQUEST['idretenue']) ? $_REQUEST['idretenue'] :      Null;
$typeDeRetenue = isset($_REQUEST['typeDeRetenue']) ? $_REQUEST['typeDeRetenue']       :       Null;
switch ($mode)
    {
	case 'nouveau': 
		// c'est une nouvelle retenue que l'on initialise
		require ("inc/classes/classretenue.inc.php");
		$retenue = new retenue(-1);
		// recherche des caractéristiques de la retenue (fictives dans le cas d'une nouvelle)
		foreach ($retenue->caracteristiques as $key=>$value)
			$$key = $value; 
		// on présente le formulaire correspondant à la retenue
		include ("inc/retenues/editretenue.inc.php");
		break;
	case 'editer':
		if (isset($idretenue))
			{
			require ("inc/classes/classretenue.inc.php");			
			$retenue = new retenue($idretenue);
			foreach ($retenue->caracteristiques as $key=>$value)
				$$key = $value;
			// on présente le formulaire correspondant à la retenue
			include ("inc/retenues/editretenue.inc.php");
			}
		else jeter();
		break;
	case 'supprimer':
		// on vérifie que l'on dispose d'un $idretenue
		if (isset($idretenue))
			{
			require ("inc/classes/classretenue.inc.php");			
			$retenue = new retenue($idretenue);
			foreach ($retenue->caracteristiques as $key=>$value)
				$$key = $value;
			// confirmation éventuelle de la suppression
			echo $retenue->formulaireSupprimer();
			}
		else jeter();
		break;
	case 'Confirmer':
		if (isset($idretenue))
			{
			require ("inc/classes/classretenue.inc.php");			
			$retenue = new retenue($idretenue);
			// suppression effective
			$retenue->supprimer($idretenue);
			redir ($_SERVER['PHP_SELF'], 
				"typeDeRetenue=$typeDeRetenue", 
				"Retenue supprimée");
			}
            else jeter();
		break;
	case 'Enregistrer': 
		if (isset($_POST))
			{
			require ("inc/classes/classretenue.inc.php");			
			$retenue = new retenue();
			// on récupère les données passées en $_POST
			$retenue->enregistrer($_POST);
			redir ($_SERVER['PHP_SELF'], 
				"mode=lister&typeDeRetenue=$typeDeRetenue", 
				"Retenue enregistrée");
			}
			else jeter();
		break;
	case 'Appliquer':
		// visitbilité des retenues
		if (isset($_POST))
			{
			require ("inc/classes/classlisteretenues.inc.php");
			$listeDeRetenues = new listesDeRetenues;
			$listeDeRetenues->cacherMontrerListe ($_POST['vis'], $typeDeRetenue);
			redir ($_SERVER['PHP_SELF'],
				"mode=lister&typeDeRetenue=$typeDeRetenue", 
				"Modifications enregistrées");
			}
		break;
    // dans tous les autres cas...
	default:        // lister les retenues
		require ("inc/classes/classlisteretenues.inc.php");
		$listeDeRetenues = new listesDeRetenues;
		echo $listeDeRetenues->formulaireChoixTypeRetenue($typeDeRetenue);

		if (isset($typeDeRetenue)) // écrire la liste des retenues du type voulu
			echo $listeDeRetenues->ecrireTableau($typeDeRetenue);
		break;
	}
echo retour();
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
