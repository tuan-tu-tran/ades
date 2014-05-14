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
EducAction\AdesBundle\User::CheckIfLogged();
include ("inc/classes/classeleve.inc.php");
require ("inc/funcdate.inc.php");
include ("config/constantes.inc.php");
Normalisation();

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : Null;
$ideleve = isset($_GET['ideleve']) ? $_GET['ideleve'] : Null;
$page = isset($_GET['page']) ? $_GET['page'] : Null;
$editPossible = (utilisateurParmi ("educ", "admin"));
if ($editPossible && $mode=="voir") {
    $menuFacts=EducAction\AdesBundle\Controller\StudentFile::CreateMenu($ideleve, $menuErrors);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link rel="stylesheet" href="config/screen.css" type="text/css" media="screen">
  <link rel="stylesheet" href="config/print.css" type="text/css" media="print">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">  
  <script language="javascript" type="text/javascript" src="inc/fonctions.js"></script>
  <script language="javascript" type="text/javascript" src="inc/onglets.js"></script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
  <?php if ($editPossible):?>
        <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
        <?php $menuFacts->RenderHead(); ?>
  <?php endif ?>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser();  //tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<?php 
switch ($mode)
	{
	case 'voir':
		$eleve = new eleve($ideleve);
			
		// inclure le menu horizontal
        if ($editPossible) {
            if ($menuErrors) {
                echo "<div>\n";
                echo "<p>Le fichier de configuration des groups de faits /local/menu_facts.ini contient des erreurs:</p>\n";
                echo "<ul>\n";
                foreach($menuErrors as $e) {
                    echo "<li>- ".EducAction\AdesBundle\Html::Encode($e)."</li>\n";
                }
                echo "</ul>\n";
                echo "</div>\n";
            }
            $menuFacts->RenderBody();
        }
		// indiquer les références de l'élève: nom, prénom et classe
		echo $eleve->NomPrClasse ($editPossible);
		// présentation de la fiche abrégée pour impression; invisible à l'écran
		echo "<div class=\"invEcran\">\n";
		echo $eleve->shortnomprclasse ();
		echo "</div>\n";
		// inclure la fiche disciplinaire
		echo $eleve->ongletsFicheDisciplinaire();
		echo $eleve->tableauxDeFaitsDisciplinaires();
		break;
	case 'nouveau':
		// nouvelle fiche vierge à présenter
		autoriser ("educ", "admin");
		$ideleve = -1;
		// pas de break, on continue sur l'édition
		
	case 'editer':
		autoriser ("educ", "admin");
		// présentation de la fiche en édition
		$eleve = new eleve($ideleve);
		$eleve->EditeNomPrClasse();
		break;
		
	case 'enregistrer':
		autoriser ("educ", "admin");
		// enregistrement de la fiche
		$eleve = new eleve(-1);
		// liste des champs du formulaire qui doivent être enregistrés
		$champs = array('nom','prenom','classe','anniv','codeInfo',
					'contrat','nomResp','courriel','telephone1','telephone2',
					'telephone3','memo','ideleve');
		$eleve->enregistrerFormulaire ($_POST,$champs);
		break;
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
