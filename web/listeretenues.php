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
  <link  type="text/css" rel="stylesheet" href="config/print.css" media="print" >
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
  <script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">
        jQuery(function($){
            var b=$("#bAdd:disabled");
            if(b.length>0){
                var p=b.position();
                var d=$("<div>");
                d.css({
                    position:"absolute",
                    display:"inline-block",
                    left:p.left,
                    top:p.top,
                    width:b.outerWidth(),
                    height:b.outerHeight(),
                    "vertical-align":"top",
                    cursor:"pointer",
                }).hover(function(){
                    overlib("Cette retenue est actuellement pleine et ne peut pas accepter d'élève supplémentaire");
                }, nd).insertAfter(b);
            }
        });
    </script>
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup -->
  </script>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
autoriser(); // tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2 class="inv">Listes des élèves en retenue</h2>
<?php
$mode = isset($_REQUEST['mode'])?$_REQUEST['mode']:Null;
$typeDeRetenue = (isset($_POST['typeDeRetenue'])?$_POST['typeDeRetenue']:Null);
$idRetenue = (isset($_REQUEST['idRetenue']))?$_REQUEST['idRetenue']:Null;

require ("inc/classes/classlisteretenues.inc.php");
$listeDeRetenues = new listesDeRetenues();
switch ($mode)
{
case 'Liste':
if (isset($idRetenue))
	{
	if (isset($typeDeRetenue))
		{
		$libelle = $listeDeRetenues->intitule($typeDeRetenue);
		echo "<h3>$libelle</h3>\n";
		}
	echo $listeDeRetenues->listeImprimable($idRetenue);
	}
	else jeter();
	break;
case 'Date':
	if (isset($typeDeRetenue))
		{
		$libelle = $listeDeRetenues->intitule($typeDeRetenue);
		echo "<h3>$libelle</h3>\n";	
		echo $listeDeRetenues->formulaireChoixDateRetenue ($typeDeRetenue);
		}
	else jeter();
	break;
default:
	echo $listeDeRetenues->formulaireChoixTypeRetenue($typeDeRetenue);
	break;
}
echo retour();
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
