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
include ("inc/fonctions.inc.php");
include ("config/constantes.inc.php");
Normalisation();
//On test si les $_Post on bien été initialisé
if(isset($_POST['ecole']) && isset($_POST['titre']))
{
	$varecole=$_POST['ecole'];
	$vartitre=$_POST['titre'];
	if($varecole && $vartitre){
		//Si vide on ne fait rien
		//variable rempli on créer le dossier
		// Rami Adrien création du fichier confdb.inc.php
		$fichierconstantesinc = fopen("config/constantes.inc.php","w");
		fwrite($fichierconstantesinc, "<?php\n");
		fwrite($fichierconstantesinc, "define( \"ECOLE\" ,\"");
		fwrite($fichierconstantesinc, $varecole);
		fwrite($fichierconstantesinc, "\");\n");
		fwrite($fichierconstantesinc, "define ( \"TITRE\" , \"");
		fwrite($fichierconstantesinc, $vartitre);
		fwrite($fichierconstantesinc, "\");\n");
		fwrite($fichierconstantesinc, "?>");		
		fclose($fichierconstantesinc);
		header("location:option.php");
		}
}
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
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Changement du nom de l'&eacute;cole</h2>
<?php
echo("<form name=\"form\" method=\"post\" action=\"confignomecole.php\">");
echo("<p><label>Ecole :</label><input name=\"ecole\" id=\"ecole\" size=\"30\" maxlength=\"50\" type=\"text\"value=\"".ECOLE."\"></p>");
echo("<p><label>Titre :</label><input name=\"titre\" id=\"titre\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".TITRE."\"></p>");
echo("<input name=\"Submit\" value=\"Enregistrer\" type=\"submit\">"); 
?>
		
</div>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
