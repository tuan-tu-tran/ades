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
include ("config/confbd.inc.php");
include ("inc/funcdate.inc.php");
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
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
  <script type="text/javascript" src="inc/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup -->
  </script>
  <script language="javascript" type="text/javascript">
<?php
$nomOuPrenom = $_GET['nomOuPrenom'];
// récupérer la liste de tous les élèves nom, prénom, classe
// récupérer la liste de tous les ideleve
// dans un Javascript
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "SELECT ideleve, nom, prenom, classe FROM ades_eleves ORDER BY $nomOuPrenom";
// echo $sql;
$resultat = mysql_query ($sql);
mysql_close ($lienDB);

/* on fabrique les deux tableaux contenant les noms d'élèves et leur id
Ces tableaux seront utilisés dans le javascript de recherche des élèves */
echo "eleves = new Array();\n";
echo "ideleves = new Array();\n";

$i = 0;
while ($ligne = mysql_fetch_array($resultat))
	{
	$ideleve = $ligne["ideleve"];
	$nom = stripslashes($ligne["nom"]);
	$prenom = stripslashes($ligne["prenom"]);
	$classe = stripslashes($ligne["classe"]);
	
	if ($nomOuPrenom == "nom")
		echo "eleves[$i] = \"$nom $prenom $classe\";\n";
		else
		echo "eleves[$i] = \"$prenom $nom $classe\";\n";
	echo "ideleves[$i] = $ideleve;\n";
	$i++;
	}
?>

function Completer(e)
	{
    if(carClavier(e).charCodeAt(0)==8)
		return true;
    saisie=document.ParNom.nom.value+carClavier(e);
    nbOccurence=0;
    indexEleve=-1;
	Liste ="";
	for (i=0; i<eleves.length; i++)
		{
		// vérifier quels élèves correspondant à la frappe effectuée
		// en faire une liste qui sera affichée dans le textarea "noms"
        	if (eleves[i].substring(0,saisie.length).toLowerCase()==saisie.toLowerCase()) 
			{
            nbOccurence++; //nombre de fois où l'on trouve le motif saisi
			Liste += "<a href='ficheel.php?mode=voir&amp;ideleve="+ideleves[i]+"'>"+eleves[i]+"</a><br />\n"; 
			// ajouter l'élève trouvé à la liste
			document.getElementById("nombre").innerHTML = nbOccurence+" élève(s)";
            indexEleve=i;
        	}
    	}
	if (nbOccurence==0)
		return false; //on n'a jamais trouve le motif

	 // afficher la liste des élèves correspondants au motif
	document.getElementById("noms").innerHTML = Liste;
	
    if (nbOccurence==1)
		{
        	// document.ParNom.nom.value=eleves[indexEleve];
		if (window.confirm(eleves[indexEleve]))
			{
			// on passe à l'adresse de la fiche de l'élève
			location.href = "ficheel.php?mode=voir&ideleve="+ideleves[indexEleve];
			}
        return false; //pour ne pas afficher le dernier caractere frappé
		}
		else 
		return true;
	}; 

// récupérer les frappes au clavier
function carClavier(e)
	{
    if (window.event)
        return String.fromCharCode(window.event.keyCode); //pour I.E.
    else
        return String.fromCharCode(e.which); //pour Mozilla
	}

</script>
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
$nomOuPrenom = isset($_GET['nomOuPrenom'])?$_GET['nomOuPrenom']:Null;
if (!($nomOuPrenom == 'prenom')) $nomOuPrenom = 'nom';
if ($nomOuPrenom == 'prenom')
	$titre = "Rechercher un élève par son prénom";
	else
	$titre = "Rechercher un élève par son nom";
$texte = file_get_contents ("inc/formParNom.inc.html");
$olib = overlib("Frappez les premières lettres du $nomOuPrenom de l'élève");
$texte = str_replace ("##TITRE##", $titre, $texte);
$texte = str_replace ("##OLIB##", $olib, $texte);
echo $texte;
echo retour();
?>

</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
