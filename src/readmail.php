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
require ("ADESminimail.class.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link rel="stylesheet" href="config/screen.css" type="text/css" media="screen">
  <link rel="stylesheet" href="config/print.css" type="text/css" media="print">
  <link rel="stylesheet" type="text/css" href="config/readmail.css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
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
?>
<div id="texte">
<?php
	$ReadMinimail = new ADESminimail;
	$ReadMinimail->connectDB();
	$ReadMinimail->getiduser($_SESSION['identification']['nom']);
	$ReadMinimail->id_mail=$_REQUEST['idmail'];
	$ReadMinimail->lu_minimail();
	$ReadMinimail->get_minimail();
	
	echo("</br><a href=\"mail.php\">retour boite de r&eacute;ception</a></br>");
	echo ("<div id=\"expediteurminimail\"><a href=\"Delmail.php?idmail=$ReadMinimail->id_mail\">Supprimer message</a>");
	echo ("</br></br>Envoy&eacute; &agrave; <b>");
	foreach($ReadMinimail->Destinataire as $i){
		echo (" : ");
		echo ($i);
		
	}
	echo (" </b> par <b> ".$ReadMinimail->nom." ".$ReadMinimail->nom);
	echo ("</b> &agrave; <i>");
	echo ($ReadMinimail->date_envoi);
	echo ("</br></br>sujet:<b>");
	echo ($ReadMinimail->sujet);
	echo ("</b>");
	echo ("</br></br><div id=\"corpmessage\">");
	echo ($ReadMinimail->texte);
	echo ("</div></div>");
	echo ("<form name=\"form\" method=\"post\" action=\"RepMail.php\">");
	//On met en paramètre caché des informations nécessaires en cas de réponse de l'utilisateur
	//Le sujet avec la notion RE pour réponse
	echo ("<input type=\"hidden\" name=\"sujet\" value=\"RE:".$ReadMinimail->sujet."\">");
	//On récupère la ref du mail pour voir les anciennes réponses si il y en a une
	echo ("<input type=\"hidden\" name=\"refidmail\" value=\"".$ReadMinimail->ref_mail."\">");
	//On récupère l'id du mail 
	echo ("<input type=\"hidden\" name=\"idmail\" value=\"".$ReadMinimail->id_mail."\">");
	echo ("<input type=\"hidden\" name=\"Sujet\" value=\"RE:".$ReadMinimail->sujet."\">");
	echo ("<input type=\"hidden\" name=\"destinataire\" value=\"".implode(",", $ReadMinimail->IdDestinataire)."\">");
	
?>
<div id="NewMail">
<textarea id="Corps" name="Corps" cols="80" rows = "15"></textarea></br></br>
<input name="Submit" value="R&eacute;pondre" type="submit"></form></br></br>
</div>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>