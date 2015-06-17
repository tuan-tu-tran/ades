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
// suppression de tous les échappements automatiques
// dans les tableaux

function Normaliser ($tableau)
{
foreach ($tableau as $clef => $valeur)
	{
	if (!is_array($valeur))
		$tableau [$clef] = stripslashes($valeur);
		else
		// appel récursif
		$tableau [$clef] = Normaliser($valeur);
	}
return $tableau;
}

function Normalisation()
{
// si magic_quotes est "ON",
if (get_magic_quotes_gpc())
	{
	$_POST = Normaliser($_POST);
	$_GET = Normaliser($_GET);
	$_REQUEST = Normaliser($_REQUEST);
	$_COOKIE = Normaliser($_COOKIE);
	}
}

function utilisateurParmi ()
{
$autorises = func_get_args();
$user = $_SESSION['identification']['privilege'];
return (in_array($user, $autorises));
}

function autoriser ()
{
// la liste des utilisateurs autorisés est passée en argument
// le nombre d'arguments est variable; la fonction func_get_args récupère cette liste
$autorises = func_get_args();
// si aucun utilisateur n'a été désigné, tout le monde est autorisé
if (count($autorises) == 0) return true;
// l'utilisateur actuel fait-il partie de la liste?
$user = $_SESSION['identification']['privilege'];
$ok = (in_array($user, $autorises));
if ($ok) return true; else jeter("Contactez votre amdinistrateur");
}

function jeter($x='')
{
echo "<script language=\"javascript\" type=\"text/javascript\">";
echo "alert('Accès refusé: $x.')\n";
echo "setTimeout (\"redirection('index.php')\",0);";
echo "</script>\n";
die();
}

function allonger($chaine, $longueur)
{
$longchaine = strlen($chaine);
$difference = $longueur - $longchaine;

if ($difference > 0)
	for ($n=0;$n<$difference; $n++)
		{$chaine .= "&nbsp;";}
return $chaine;
}

function redir ($page, $parametres, $message, $temps=1000)
{
echo "<script language=\"JavaScript\">\n";
echo "setTimeout('redirection(\"$page?$parametres\")',$temps)\n";
echo "</script>\n";
echo "<p class=\"avertissement\">$message</p>\n";
}

function quiEstLa ()
{
$ip = $_SERVER['REMOTE_ADDR'];
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$date = date("d-m-Y");
$heure = date("H:i");
if(isset($_SESSION['identification']['nom']) || isset($_SESSION['identification']['prenom'])){
	$who = isset($_SESSION['identification']['nom'])?$_SESSION['identification']['nom']:"";
	$who.=" ";
	$who .= isset($_SESSION['identification']['prenom'])?$_SESSION['identification']['prenom']:"";
}else{
	$who="Non identifié";
}
$texte = "Nous sommes le <strong>$date</strong> à <strong>$heure</strong>.<br />\n";
$texte .= "Votre adresse IP: <strong>$ip</strong>";
if($hostname!=$ip){
	$texte .= " (<strong>$hostname</strong>)";
}
$texte.=".<br />\n";
$texte .= "Vous êtes <strong>$who</strong>.";
return $texte;
}

// fonction émulant scandir pour PHP4
/* function scandir4 ($dir, $tri)
{
$dh  = opendir($dir);
while (false !== ($filename = readdir($dh))) 
    $files[] = $filename;
closedir($dh);
if ($tri == 1)
	sort($files);
	else rsort ($files);
return $files;
} */

function selectChampFormulaire ($champ)
{
$texte = "<script language=\"javascript\" type=\"text/javascript\">\n";
$texte .= "document.forms[0].elements['$champ'].focus();\n";
$texte .= "</script>\n";
return $texte;
}

function afficher ($tableau, $die=false)
{
if (count($tableau) == 0)
	echo "Tableau vide";
	else
	{
	echo "<pre>";
	print_r ($tableau);
	echo "</pre>";
	echo "-------------<br />";
	}
if ($die) die();
}

function images ($type, $idfait, $ideleve=-1)
{require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sqlClasses = "SELECT DISTINCT classe FROM ades_eleves";
$classes = mysql_query ($sqlClasses);
mysql_close ($lienDB);

$precedent = '1';
$lesClasses = "<h3>Memento des classes</h3>\n<ul><li>";
while ($ligne = mysql_fetch_row($classes))
	{
	$actuel = $ligne[0];
	$car1 = substr($actuel, 0, 1);
	if ($car1<>$precedent)
		$lesClasses .= "</li>\n<li>";
	$lesClasses .= "$actuel :";
	$precedent = $car1;
	}
$lesClasses .= "</li>\n";
switch ($type)
{
case "edit":
	$img = "<a href=\"fact/edit/$idfait\">";
	$img .= "<img src=\"images/editer.png\" alt=\"editer\" title=\"Modifier\" ";
	$img .= "border=\"0\"></a>";
	break;
case "suppr":
	$img = "<a href=\"fait.php?mode=confirmer&amp;ideleve=$ideleve&amp;idfait=$idfait\">";
	$img .= "<img src=\"images/suppr.png\" alt=\"supprimer\" title=\"Supprimer\" ";
	$img .= "border=\"0\"></a>";
	break;
case "print":
	$img = "<a href=\"imprretenue.php?idfait=$idfait\" target=\"_blank\">";
	$img .= "<img src=\"images/i.gif\" alt=\"imprimer\" title=\"Imprimer\" ";
	$img .= "border=\"0\"></a>";
	break;
}
return $img;
}  // function images

function mementoClasses ()
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sqlClasses = "SELECT DISTINCT classe FROM ades_eleves ORDER BY classe";
$resultat = mysql_query ($sqlClasses);
mysql_close ($lienDB);

$tableauClasses = array();
// chaque ligne du tableau isole un niveau (toutes les 1ères, toutes les 2èmes...)
while ($classes = mysql_fetch_assoc($resultat))
	{
	$laClasse = $classes['classe'];
	$car1 = substr($laClasse,0,1);
	$tableauClasses[$car1][]=$classes['classe'];
	}
$liste = "<h4>Classes</h4>";
foreach ($tableauClasses as $unNiveau)
	{
	$leNiveau = $unNiveau[0][0];
	$liste .= "<ul>\t<li><a href=\"javascript:completer('$leNiveau')\" title=\"Niveau $leNiveau e\" ##OLIB1##>$leNiveau > </a></li>";
	foreach ($unNiveau as $uneClasse)
		$liste .= "\t<li><a href=\"javascript:completer('$uneClasse')\" title=\"$uneClasse\" ##OLIB2##>$uneClasse</a></li>\n";
	$liste .= "</ul>\n";
	}
$olib1 = overlib("Cliquer sur le bouton pour sélectionner tout le niveau");
$olib2 = overlib("Cliquer sur le bouton pour sélectionner cette classe");

$liste = str_replace ("##OLIB1##", $olib1, $liste);
$liste = str_replace ("##OLIB2##", $olib2, $liste);
return $liste;
}

function entetePageHTML ($titre='ADES')
{
$texte = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
$texte .= "<html>\n<head>\n";
$texte .= "<meta content=\"text/html; charset=ISO-8859-1\" http-equiv=\"content-type\">\n";
$texte .= "<title>##ECOLE##</title>\n";
$texte .= "<link rel=\"stylesheet\" href=\"config/screen.css\" type=\"text/css\" media=\"screen\">\n";
$texte .= "<link rel=\"stylesheet\" href=\"config/print.css\" type=\"text/css\" media=\"print\">\n";
$texte .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"config/calendrier.css\">\n";
$texte .= "</head>\n<body>\n";
$texte = str_replace("##ECOLE##", $titre, $texte);
return $texte;
}

function finPageHTML ()
{
$texte = "</body>\n</html>\n";
return $texte;
}

function telecharger ($fichier)
{
$texte = "<div style=\"text-align:center\">\n";
$texte .= "<a href=\"$fichier\" target=\"_blank\" title=\"Télécharger\" ##OLIB##>";
$texte .= "Cliquez ici pour télécharger le fichier <strong>##FICHIER##</strong></a>\n</div>\n";

$olib = "Clic du bouton droit. Enregistrer la cible sous...<br />";
$olib .= "Puis ouvrir avec un traitement de textes.";
$olib = overlib($olib);

$texte = str_replace("##OLIB##", $olib, $texte);
$texte = str_replace("##FICHIER##", $fichier, $texte);
return $texte;
}

function retour ($parametre="")
{
if (!$parametre == "")
	$texte = $parametre;
	else $texte="Retour à la page précédente";
$lien = "<div class=\"inv\" style=\"text-align:center; margin: 1em 0 2em 0\"><a href=\"javascript:history.go(-1)\">$texte</a></div>";
return $lien;
}

function retourIndex ($parametre="")
{
if (!$parametre == "")
	$texte = $parametre;
	else $texte="Retour à la page d'accueil";
$lien = "<div class=\"inv\" style=\"text-align:center; margin: 1em 0 2em 0;\"><a href=\"index.php\">$texte</a></div>";
return $lien;
}

function overlib ($hint)
{
$hint = addslashes($hint);
$texte = " onmouseover=\"return overlib('$hint');\" onmouseout=\"return nd();\"";
return $texte;
}

function menu ($texte, $icone, $lien, $titre="")
{
$menu = "\t<li><a href=\"##LIEN##\" title=\"##TITRE##\">##ICONE####TEXTE##</a></li>\n";
$icone = "<img src=\"images/$icone\" class=\"icone\" alt=\".\">";
$menu = str_replace ("##LIEN##", $lien, $menu);
$menu = str_replace ("##LIEN##", $lien, $menu);
$menu = str_replace ("##TEXTE##", $texte, $menu);
$menu = str_replace ("##ICONE##", $icone, $menu);
return $menu;
}

/**
 * Redirect to a location using header php function
 * then exit
 */
function redirect($location){
	header("Location: $location");
	exit;
}
?>
