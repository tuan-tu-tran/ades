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
require ("config/confbd.inc.php");
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
/* Rami Adrien
 * Fonction synthèse du nombre d'éleves et de classe traiter
 * ---------------------------------------------------------
 * 
 * La fonction affichant le nombre d'élèves et de classes traiter a été déplacé et à subit un remaniement.
 * Il y a suppresion de l'utilisation d'un système de fichier html à part pour l'intégré directement dans le code
 * pour allégé le code
 */
//On récupére la date et l'heure
$date = date("d/m/Y");
$heure = date("H:i");
//On se connecte à la db
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd) or die('erreur 1'.mysql_error());
mysql_select_db($sql_bdd);

// recherche de toutes les classes et tous les élèves existants dans la base de données
$sqlClasses = "SELECT DISTINCT classe FROM ades_eleves";
$sqlEleves = "SELECT DISTINCT idunique FROM ades_eleves";

$Classes = mysql_query($sqlClasses) or die('erreur 2'.mysql_error()) ;
$Eleves = mysql_query($sqlEleves) or die('erreur 3'.mysql_error());
mysql_close ($lienDB);

$nbClasses = mysql_num_rows($Classes);
$nbEleves = mysql_num_rows($Eleves);
?>
<fieldset id="cadreGauche">
<legend>Informations sur l'&eacute;cole</legend>
<p>Nous sommes le <strong><?php echo($date); ?></strong> et il est déjà&nbsp;<strong><?php echo($heure); ?></strong></p>
<p>A l'heure qu'il est, nous traitons</p>
<ul>
  <li><?php echo($nbClasses); ?> classes</li>
  <li><?php echo($nbEleves); ?> élèves</li>
</ul>
</fieldset>
<div id="texte">
<h2>Synthèses</h2>
<?php
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : Null;
$typeSynthese = isset($_POST['typeSynthese']) ? $_POST['typeSynthese'] : Null;
$date1 = isset($_POST['date1']) ? $_POST['date1'] : Null;
$date2 = isset($_POST['date2']) ? $_POST['date2'] : Null;
$classe = isset($_POST['classe']) ? $_POST['classe'] : Null;

$synthese = new synthese();
if ($mode != "Envoyer")
	{
	$texte = file_get_contents ("inc/synthese/formsynthese.inc.html");
	$texte = str_replace("##ADRESSE##", $_SERVER['PHP_SELF'], $texte);
	$texte = str_replace("##LESCLASSES##", mementoClasses(), $texte);
	echo $texte;
	}
	else
	{
	// produire la requête MySQL correspondant à la synthèse voulue
	$resultat = $synthese->requeteSynthese ($_POST);
	// le tableau $resultat contient toutes les informations demandées
	if (count($resultat) != 0)
	switch ($typeSynthese)
		{	
		case 'faits':
			// synthèse avec détails fait par fait
			$texte = $synthese->syntheseFaits ($date1, $date2, $classe, $resultat);
			echo $texte;
			break;
		case 'eleves':
			// fiche disciplinaire par élève
			$texte = $synthese->syntheseEleves($date1, $date2, $classe, $resultat);
			echo $texte;
			break;
		}  // switch
	} // else
echo retour();
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
