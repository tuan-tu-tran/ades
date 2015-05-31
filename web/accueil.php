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

/*Rami Adrien: Processus d'installation automatique
 * On test si le fichier confdb.inc.php existe
 * Si il n'existe pas on lance le processus d'installation d'ADES avec le fichier creation.php
 * Si il existe on laisse le programme se lancé normalement
 */
EducAction\AdesBundle\Install::CheckIfNeeded();
EducAction\AdesBundle\Upgrade::CheckIfNeeded();
require_once "config/constantes.inc.php";

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
<script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
	<style type="text/css">
		#cadreDroit strong{padding:0;}
		#cadreDroit p{padding-left:0;}
	</style>
</head>
<body>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
<?php
// autorisations pour la page
// tout le monde
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2 style="clear:both"> Connexion </h2>

<fieldset id="cadreGauche"> 
<legend>Veuillez vous identifier</legend> 

<form name="form1" method="post" action="login.php" onsubmit="return verif(this)"> 
<p><span class="label">Utilisateur :</span> 
<input name="user" value="" size="20" maxlength="30" class="obligatoire" type="text"></p>
<p><span class="label">Mot de passe :</span> 
<input name="mdp" value="" size="20" class="obligatoire" type="password"></p> 
<div style="text-align: center;"> 
<input name="Submit" value="Connexion" type="submit"> </div>
</form>
<?php 
// ligne à inclure pour la démo
// include ("noticedemo.inc.php"); 
?>
</fieldset>

<fieldset id="cadreDroit">
<legend> Attention!</legend> 
<p>Votre passage sur cette page est enregistré.</p>
<?php echo quiEstLa(); ?>
</fieldset>

<script language="javascript" type="text/javascript">
document.forms[0].elements[0].select();
</script>

<?php 
$erreur = (isset($_REQUEST['erreur']))?$_REQUEST['erreur']:Null;
if ($erreur)
	{
	$texte = "<script language=\"javascript\" type=\"text/javascript\">";
	switch ($erreur)
		{
		case 'faux':
		$texte .= "alert('Nom d\'utilisateur ou mot de passe incorrect. Recommencez.')";
        break;
	case 'manque':
        $texte .= "alert('Le nom d\'utilisateur et/ou le mot de passe manquent. ";
		$texte .= "Recommencez.')";
        break;
	case 'droits':
        $texte .= "alert('Vos droits sont insuffisants pour accéder à cette page.\\n";
		$texte .= "Veuillez contacter votre administrateur.')";
        break;
	}
	$texte .= "</script>\n";
	echo $texte;
	}
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
<script language="javascript" type="text/javascript">
document.forms[0].elements[0].select();
</script>
</body>
</html>
