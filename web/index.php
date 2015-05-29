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
require "inc/init.inc.php";
EducAction\AdesBundle\Install::CheckIfNeeded();
EducAction\AdesBundle\Controller\Upgrade::CheckIfNeeded();
EducAction\AdesBundle\User::CheckIfLogged();

require ("config/constantes.inc.php");
require ("config/confbd.inc.php");
//Rami Adrien
//Classe qui va permettre la gestion des mémos de l'utilisateur
require ("ADEStodo.class.php");
//On créer un objet ADEStodo
$ADESmemo = new ADEStodo;
//On se connecte à la db
$ADESmemo->connectDB();
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
  <script type="text/javascript" type="text/javascript" src="ADESMemo.js"></script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<!--[if IE]>
<link href="css/facelist_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser();  // tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<?php
/*Rami Adrien
 * Les fonctionnalités de départ on été déplacé dans respectivement dans:
 * Synthèse (pour dire le nombre d'élèves actuellement traité
 * Backup, pour dire quelle est le dernier backup
 * La fonction permettant d'afficher les fêtes a été simplifié et n'utilise pas l'option
 * du str replace avec le fichier text index.inc.htm
 */

//On récupère la date et l'heure
$date = date("d/m/Y");
$heure = date("H:i");
$dateAnniv = date ("d/m");
//On se connecte à la db
//$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
//mysql_select_db ($sql_bdd);

// recherche de toutes les classes et tous les élèves existants dans la base de données
$sqlAnniv = "SELECT ideleve, classe, nom, prenom FROM ades_eleves WHERE anniv ='$dateAnniv'";

$Anniv = mysql_query($sqlAnniv);
//mysql_close ($lienDB);

$olib = overlib("Anniversaire de ce jour.");
$anniversaires = "";
if (@mysql_num_rows($Anniv) > 0)
	while ($ligne = mysql_fetch_array($Anniv))
		{
		$ideleve = $ligne['ideleve'];
		$classe = $ligne['classe'];
		$prenom = $ligne['prenom'];
		$nom = $ligne['nom'];
		$anniversaires .= "<p><a href=\"ficheel.php?mode=voir&amp;ideleve=##IDELEVE##\" ";
		$anniversaires .= "##OLIB##>##PRENOM## ##NOM## ##CLASSE##</a></p>\n";

		$anniversaires = str_replace("##IDELEVE##", $ideleve, $anniversaires);
		$anniversaires = str_replace("##OLIB##", $olib, $anniversaires);
		$anniversaires = str_replace("##NOM##", $nom, $anniversaires);
		$anniversaires = str_replace("##PRENOM##", $prenom, $anniversaires);
		$anniversaires = str_replace("##CLASSE##", $classe, $anniversaires);
		}
		$ADESmemo->getiduser($_SESSION['identification']['nom']);
		
		
?>
<h2>Accueil</h2>

<fieldset id="CadreAccueilMemo">
<legend>Memo</legend>
<form name="formajoutmemo">
<input type="text" id="memoAAjouter" name="memoAAjouter" size="30" onKeyPress="if (event.keyCode == 13) AjouterMemo();" ><input value="Ajouter" type="button" onClick="AjouterMemo();">
</form>
<div id="delmemo">
<?php
echo $ADESmemo->send_todo_info();
?>
</div>
</fieldset>
<fieldset id="cadreDroit">
<legend>Anniversaires</legend>
<?php if($anniversaires!=NULL):?>
<h3>Joyeux Anniversaire &agrave;</h3>
<?php 
echo($anniversaires);
?>
<?php else:?>
<p>Pas d'anniversaire aujourd'hui.</p>
<?php endif;?>
</fieldset>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
