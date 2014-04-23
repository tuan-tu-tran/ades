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
 * Module d'option/ Configuration du billet de retenue:
 * C'est à partir de ce menu que l'administrateur va pouvoir configurer le billet de retenue à sa guise
 * 
 */
 //On inclut les librairies de fonctions de constantes et priv�
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
include("config/confbilletretenue.inc.php");
?>
<div id="texte">
<h2>Configuration billet retenue</h2>
		<?php
		//Zone affichant les paramètres actuel avec un formulaire afin de les modifier
		echo("<form name=\"form\" method=\"post\" action=\"configurationbilletretenue.php\">\n");
		echo("<label>Type Impression :</label><select name=\"typeimpression\" id=\"typeimpression\">");
		//On détecte ici le type d'impression et on sélectionne le bon élément selon la configuration du billet
		if($typeimpression == "Paysage")
		{
			// On selectionne paysage si c'est paysage qui est enregistré en fichier de configuration
			echo("<option selected=\"selected\">Paysage</option><option>Portrait</option></select><br/><br/>\n");
		}else
		{
			// Sinon on sélectionne l'autre
			echo("<option>Paysage</option><option selected=\"selected\">Portrait</option></select><br/><br/>\n");
		}
		//On charge le reste des données dans le formulaire selon le fichier de configuration
		echo("<label>Image de l'&eacute;cole :</label><input name=\"imageecole\" id=\"imageecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$imageenteteecole \"><br/><br/>\n");
		echo("<label>Nom de l'&eacute;cole :</label><input name=\"nomecole\" id=\"nomecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$nomecole\"><br/><br/>\n");
		echo("<label>Adresse de l'&eacute;cole :</label><input name=\"adresseecole\" id=\"adresseecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$adresseecole\"><br/><br/>\n");
		echo("<label>Lieu :</label><input name=\"lieu\" id=\"lieu\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$lieuecole \"><br/><br/>\n");
		echo("<label>Signature 1 :</label><input name=\"signature1\" id=\"signature1\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$signature1\"><br/><br/>\n");
		echo("<label>Signature 2 :</label><input name=\"signature2\" id=\"signature2\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$signature2 \"><br/><br/>\n");
		echo("<label>Signature 3 :</label><input name=\"signature3\" id=\"signature3\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"$signature3 \"><br/><br/>\n");
		// l'utilisateur à la posibilité de voir un aperçu ou d'enregistrer les paramètres
		?>
		<input name="Submit" value="Enregistrer" type="submit"><br/><br/> 
		<a href='javascript:popup("apercubilletretenue.php")'>Visualiser un aper&ccedil;u du billet de retenue</a>
		
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
