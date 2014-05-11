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
if(isset($_POST['typeimpression']))
{	
	if($_FILES['fichierimagebilletretenue']['error']==0)
	{	
		if(move_uploaded_file($_FILES['fichierimagebilletretenue']['tmp_name'], "config/".$_FILES['fichierimagebilletretenue']['name']))
		{
			chmod("config/".$_FILES['fichierimagebilletretenue']['name'], 777);
			$nometcheminfichier = "config/".$_FILES['fichierimagebilletretenue']['name'];
		}else{
		echo("Echec du changement du logo");
		}
	}else
	{
		$nometcheminfichier = $imageenteteecole;
	}
	// Rami Adrien création du fichier confdb.inc.php
	$fichierconfbillet = fopen("config/confbilletretenue.inc.php","w");
	fwrite($fichierconfbillet, "<?php\n");
	fwrite($fichierconfbillet, "//Rami Adrien\n");
	fwrite($fichierconfbillet, "//Fichier qui permet de définir tout les variables et paramètre du billet de retenue\n");
	fwrite($fichierconfbillet, "\$typeimpression =\"".$_POST['typeimpression']."\";\n");
	fwrite($fichierconfbillet, "\$imageenteteecole =\"".$nometcheminfichier."\";\n");
	fwrite($fichierconfbillet, "\$nomecole =\"".$_POST['nomecole']."\";\n");
	fwrite($fichierconfbillet, "\$adresseecole =\"".$_POST['adresseecole']."\";\n");
	fwrite($fichierconfbillet, "\$telecole =\"".$_POST['telecole']."\";\n");
	fwrite($fichierconfbillet, "\$lieuecole =\"".$_POST['lieu']."\";\n");
	fwrite($fichierconfbillet, "\$signature1 =\"".$_POST['signature1']."\";\n");
	fwrite($fichierconfbillet, "\$signature2 =\"".$_POST['signature2']."\";\n");
	fwrite($fichierconfbillet, "\$signature3 =\"".$_POST['signature3']."\";\n");
	fwrite($fichierconfbillet, "?>");
	fclose($fichierconfbillet);
	echo("Configuration du billet enregistr&eacute;");
}

?>
<div id="texte">
<h2>Configuration billet retenue</h2>
		<?php
		//Zone affichant les paramètres actuel avec un formulaire afin de les modifier
		echo("<form name=\"form\" method=\"post\" action=\"configurationbilletretenue.php\" enctype=\"multipart/form-data\">\n");
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
		echo("<label>Image de l'&eacute;cole :</label><input type=\"file\" name=\"fichierimagebilletretenue\"><br/><br/>\n");
		echo("<label>Nom de l'&eacute;cole :</label><input name=\"nomecole\" id=\"nomecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($nomecole, ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Adresse de l'&eacute;cole :</label><input name=\"adresseecole\" id=\"adresseecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($adresseecole, ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Lieu :</label><input name=\"lieu\" id=\"lieu\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($lieuecole , ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Telephone :</label><input name=\"telecole\" id=\"telecole\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($telecole , ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Signature 1 :</label><input name=\"signature1\" id=\"signature1\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($signature1, ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Signature 2 :</label><input name=\"signature2\" id=\"signature2\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($signature2 , ENT_QUOTES)."\"><br/><br/>\n");
		echo("<label>Signature 3 :</label><input name=\"signature3\" id=\"signature3\" size=\"30\" maxlength=\"50\" type=\"text\" value=\"".htmlentities($signature3 , ENT_QUOTES)."\"><br/><br/>\n");
		// l'utilisateur à la posibilité de voir un aperçu ou d'enregistrer les paramètres
		?>
		<input name="Submit" value="Enregistrer" type="submit"><br/><br/> 
		<a href="apercubilletretenue.php" target=_blank>Visualiser un aper&ccedil;u du billet de retenue</a>
		
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
