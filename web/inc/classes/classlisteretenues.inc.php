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
//------------------------------------------------------------------------------------
// cette classe maintient X listes des X types de retenues possibles
// date, local, places, durée, occupation,...
//------------------------------------------------------------------------------------

class listesDeRetenues {
    var $listes = array();
    var $intitulesDesRetenues = array();

    //------------------------------------------------------------------------------------
    // fonction constructeur
    function __construct () {
        // lire la liste des types de retenues avec leur intitulé
        $this->intituleDesRetenues = parse_ini_file("config/intitulesretenues.ini", TRUE);

        // construit les listes pour tous les types de retenues
        require ("config/confbd.inc.php");
        $lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
        mysql_select_db ($sql_bdd);
        $sql = "SELECT * FROM ades_retenues ";
        $sql .= "ORDER BY typeDeRetenue, ladate";
        // echo $sql;
        $resultat = mysql_query ($sql);
        mysql_close ($lienDB);

        // on construit X listes où X = nombre de types de retenues
        // dans chaque liste, on empile les caractéristiques des retenues de ce type
        while ($ligne = mysql_fetch_assoc($resultat)) {
            $typeDeRetenue = $ligne['typeDeRetenue'];
            $this->listes[$typeDeRetenue][] = $ligne;
        }
    }
    //------------------------------------------------------------------------------------
function intitule ($typeDeRetenue)
{
return ($this->intituleDesRetenues[$typeDeRetenue]['intitule']);
}

//------------------------------------------------------------------------------------
// retourne sous la forme d'un texte HTML/Javascript
// la liste des retenues disponibles sous la forme d'une liste déroulable

function listeOptions ($typeDeRetenue, $id=-1, $desactiver=false)
{
$laliste = "";
$listeBrute = isset($this->listes[$typeDeRetenue])?$this->listes[$typeDeRetenue]:NULL;
// voyons si des retenues sont déjà définies
if (count($listeBrute) > 0)
	foreach ($listeBrute as $UneRetenue)
		{
		// on ne considère que les retenues à afficher (pas les retenues cachées)
		if ($UneRetenue['affiche'] == "O")
			{
			$idretenue = $UneRetenue['idretenue'];
			$places = $UneRetenue['places'];
			$occupation = $UneRetenue['occupation'];
			$reste = $places - $occupation;
			$duree = $UneRetenue['duree'];
			$heure = $UneRetenue['heure'];
			$date = allonger(date_sql_php($UneRetenue['ladate']), 12);
		
			$option = "<option value=\"$idretenue\" title=\"$reste place(s)\"";
			// Une seule des deux options possibles: "disabled" ou "selected"
			// une retenue "selected" ne peut jamais être "disabled"
			if ($id == $idretenue)
				$option .= " selected=\"selected\"";
				// alors elle est  marquée comme "selected"
				// sinon, s'il faut désactiver, elle est marquée comme "disabled"
				else if ($desactiver)
					$option .= ($reste<=0)?" disabled=\"disabled\"":"";
			$option .= ">";
			$option .= "$duree h le $date à $heure";
			$option .= "</option>\n";
			$laliste .= $option;
			}
		}
return $laliste;
}

function cacherMontrerListe ($liste, $typeDeRetenue)
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
// relire l'ensemble des retenues avec leur propriété d'affichage
$sql = "SELECT idretenue, affiche FROM ades_retenues WHERE typeDeRetenue = $typeDeRetenue;";
// echo "$sql <br />";
$resultat = mysql_query ($sql);
// on passe chaque retenue de la BD en revue
while ($ligne = mysql_fetch_assoc($resultat))
	{
	$idretenue = $ligne['idretenue'];
	$affiche = $ligne['affiche'];
	// si la retenue est dans la liste des "affichées",
	// alors il faut éventuellement changer le contenu de la BD
	
	if (in_array ($idretenue, $liste))
		$sql = "UPDATE ades_retenues SET affiche ='O' WHERE idretenue = $idretenue";
		else
		$sql = "UPDATE ades_retenues SET affiche ='N' WHERE idretenue = $idretenue";
	// echo "$sql <br />\n";
	// s'il y a une modification de la BD à faire, on la fait
	$modification = mysql_query($sql);
	}
mysql_close ($lienDB); 
}

function caseACocher ($valeur, $idretenue)
{
$case = "<input name=\"vis[]\" value=\"$idretenue\" type=\"checkbox\" ";
$case .= $valeur=="O"?"checked":"";
$case .= " title=\"Cocher pour afficher la retenue\">";
return $case;
}

//------------------------------------------------------------------------------------
// impression d'un tableau de la liste des retenue d'un type
//------------------------------------------------------------------------------------
function ecrireTableau ($typeDeRetenue)
{
$nombre = count($this->listes[$typeDeRetenue]);
if ($nombre == 0) return ;
	
$debutTable = "<form name=\"cocher\" method=\"POST\" action={$_SERVER['PHP_SELF']}>\n";
$debutTable .= "<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">\n";
$finTable = "</table>\n";
$finTable .= "<input name=\"typeDeRetenue\" value=\"$typeDeRetenue\" type=\"hidden\">\n";
$finTable .= "<p style=\"float:right\">Pour les cases cochées: \n";
$finTable .= "<input type=\"submit\" name=\"mode\" value=\"Appliquer\">\n";
$finTable .= "<input type=\"reset\" name=\"Annuler\" value=\"Annuler\" >\n</p>\n";
$finTable .= "</form>\n</p>\n";
$imgedt = "<img src=\"images/editer.png\" width=\"16\" height=\"16\" border=\"O\" alt=\"editer\" title=\"editer\">";
$imgsup = "<img src=\"images/suppr.png\" width=\"16\" height=\"16\" border=\"O\" alt=\"supprimer\" title=\"supprimer\">";

$intitule = $this->intitule($typeDeRetenue);

$tableau = "<h2>Type: $intitule :: Nombre : $nombre</h2>\n";
$tableau .= "$debutTable\n";
$tableau .= "<tr style=\"text-align:center\">\n\t<td width=\"16\">&nbsp;</td>\n";
$tableau .= "\t<td>Date</td>\n";
$tableau .= "\t<td>Heure</td>\n";
$tableau .= "\t<td>Durée</td>\n";
$tableau .= "\t<td>Local</td>\n";
$tableau .= "\t<td>Places</td>\n";
$tableau .= "\t<td>Occupation</td>\n";
$tableau .= "\t<td>Visible</td>\n";
$tableau .= "\t<td style=\"width:4em\">Editer</td>\n";
$tableau .= "\t<td style=\"width:4em\">Liste</td>\n";
$showAdd = FALSE;
foreach ($this->listes[$typeDeRetenue] as $UneRetenue){
    if($UneRetenue["occupation"] < $UneRetenue["places"]){
        $tableau .= "\t<td style=\"width:4em\">Ajouter<br/>un élève</td>\n";
        $showAdd=TRUE;
        break;
    }
}


foreach ($this->listes[$typeDeRetenue] as $UneRetenue)
	{
	$idretenue = $UneRetenue['idretenue'];
	$classeCSS = ($UneRetenue['places'] > $UneRetenue['occupation']) ? "libre" : "rempli";
	$classeCSS .= ($UneRetenue['affiche'] == 'O') ? "montre" : "cache";
	$titre  = ($UneRetenue['affiche'] == 'O') ? "Retenue visible" : "Retenue non visible";
	$tableau .= "<tr class=\"".$classeCSS."\" title=\"$titre\">\n";
	// on ne prÃ©sente la possibilité de suppression que pour les retenues non assignées à un éléve
	// préservation de l'intégrité référentielle
	if ($UneRetenue['occupation'] == 0)
		$tableau .= "\t<td><a href=\"retenue.php?mode=supprimer&amp;idretenue=$idretenue\">$imgsup</a></td>\n";
		else $tableau .= "\t<td>&nbsp;</td>";
	$tableau .= "\t<td>".date_sql_php($UneRetenue['ladate'])."</td>\n";
	$tableau .= "\t<td>".$UneRetenue['heure']."</td>\n";
	$tableau .= "\t<td>".$UneRetenue['duree']."h</td>\n";
	$tableau .= "\t<td>".$UneRetenue['local']."</td>\n";
	$tableau .= "\t<td>".$UneRetenue['places']."</td>\n";
	$tableau .= "\t<td>".$UneRetenue['occupation']."</td>\n";
	$tableau .= "\t<td style=\"text-align:center\">".$this->caseACocher($UneRetenue['affiche'], $UneRetenue['idretenue'])."</td>\n";
    $tableau .= "\t<td style='text-align:center'><a href=\"retenue.php?mode=editer&amp;idretenue=$idretenue\">$imgedt</a></td>\n";
    $tableau .= "\t<td style='text-align:center'><a href='' ".overlib("Voir la liste des élèves de cette retenue")." title=''><img style='width:16px; height:16px' src='images/list.png' alt='list des élèves'/></a></td>\n";
    if($showAdd){
        $tableau .= "\t<td style='text-align:center'>";
        if($UneRetenue["occupation"] < $UneRetenue["places"]){
            $tableau .= "<a href='detention/add/$idretenue' ".overlib("Ajouter un élève à la retenue")." title=''><img src='images/add.png' width='16' height='16' border='0' alt='ajouter' ></a>\n";
        }
        $tableau .= "</td>\n";
    }
	$tableau .= "</tr>\n";	
	}
$tableau .= $finTable;
return $tableau;
}

//------------------------------------------------------------------------------------
// liste des éléves inscrits à la retenue de $idretenue
// à l'attention de la personne qui surveille la retenues
//------------------------------------------------------------------------------------
function listeImprimable ($idretenue)
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
// on récupère les caractéristiques générales de la retenue
$sql = "SELECT * FROM ades_retenues WHERE idretenue = '$idretenue'";
// echo $sql;
$resultat = mysql_query($sql);
$ligne = mysql_fetch_assoc($resultat);

$ladate = date_sql_php($ligne['ladate']);
$heure = $ligne['heure'];
$duree = $ligne['duree'];
$local = $ligne['local'];
$typeDeRetenue = $ligne['typeDeRetenue'];
$places = $ligne['places'];

// requéte pour récupérer les informations de la liste
$sql  = "SELECT ades_eleves.nom, ades_eleves.prenom, ades_eleves.classe, ";
$sql .= "ades_faits.motif, ades_faits.professeur, ades_faits.travail ";
$sql .= "FROM ades_retenues LEFT JOIN ades_faits ";
$sql .= "ON ades_retenues.idretenue = ades_faits.idretenue ";
$sql .= "LEFT JOIN ades_eleves ON ades_faits.ideleve = ades_eleves.ideleve ";
$sql .= "WHERE ades_retenues.idretenue='$idretenue' AND ";
$sql .= "ades_faits.supprime != 'O' ORDER BY nom,prenom";
// echo $sql;
$resultat = mysql_query ($sql);
mysql_close ($lienDB);

$nombre = mysql_num_rows($resultat);
if(!isset($texte)){$texte="";}
$texte .= "<h3>Date : $ladate à $heure :: Durée : $duree h </h3>\n";

if ($nombre>0)
	{
	$texte .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
	$texte .= "<tr style=\"text-align:center\">";
	$texte .= "\t<td width=\"2em\">&nbsp;</td>\n";
	$texte .= "\t<td>Nom de l'élève</td>\n";
	$texte .= "\t<td>Classe</td>\n";
	// $texte .= "\t<td>Motif de la retenue</td>\n";
	$texte .= "\t<td>Travail à effectuer</td>\n";

	$texte .= "\t<td>Professeur</td>\n";
	$texte .= "\t<td>Présent</td>\n";
	$texte .= "\t<td>Signé</td>\n";
	$texte .= "</tr>\n";
	
	$html = "<tr>\n\t<td width=\"2em\">##I##</td>\n";
	$html .= "\t<td>\n<strong>##NOM## ##PRENOM##</strong></td>\n";
	$html .= "\t<td style=\"text-align:center\">##CLASSE##</td>\n";
	// $html .= "\t<td>##MOTIF</td>\n";
	$html .= "\t<td>##TRAVAIL##</td>\n";
	$html .= "\t<td>##PROFESSEUR##</td>\n";
	$html .= "\t<td>&nbsp;</td>\n";
	$html .= "\t<td>&nbsp;</td>\n";
	$html .= "</tr>\n";
	$i=1;
	while ($ligne = mysql_fetch_assoc($resultat))
		{
		$nom = $ligne['nom'];
		$prenom = $ligne['prenom'];
		$classe = $ligne['classe'];
		$motif = $ligne['motif'];
		$travail = $ligne['travail'];
		$professeur = $ligne['professeur'];
		$texte .= $html;
		$texte = str_replace("##I##", $i, $texte);
		$texte = str_replace("##NOM##", $nom, $texte);
		$texte = str_replace("##PRENOM##", $prenom, $texte);
		$texte = str_replace("##CLASSE##", $classe, $texte);
		$texte = str_replace("##MOTIF##", $motif, $texte);
		$texte = str_replace("##TRAVAIL##", $travail, $texte);
		$texte = str_replace("##PROFESSEUR##", $professeur, $texte);
		
		$i++;
		}
	$texte .= "</table>\n";
	$texte .= "<h3 style=\"border-bottom: 2px solid black; margin-bottom: 2em;\">";
	$texte .= "Remarques du surveillant</h3>";
	}
	else
	$texte .= "<strong>Aucune inscription à ce jour.</strong>\n";

return $texte;
}
//------------------------------------------------------------------------------------
function formulaireChoixDateRetenue ($typeDeRetenue=0)
{
// constituer la liste déroulante des retenues de type $typeDeRetenue,
$select = $this->listeOptions ($typeDeRetenue);

$form = "";
// Vérifier si des retenues existent déjà pour ce type
if (strlen($select) > 0)
	{
	$form .="<form name=\"form1\" method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">\n";
	$form .= "<select size=\"1\" name=\"idRetenue\">\n";
	$form .= $select;
	$form .= "</select>\n";
	$form .= "<a href=\"void(0)\" ##OLIB##>?</a>\n";
	$form .= "<input name=\"typeDeRetenue\" value=\"$typeDeRetenue\" type=\"hidden\">";
	$form .= "<input name=\"mode\" value=\"Liste\" type=\"submit\">";
	$form .= "</form>\n";
	$olib = overlib("Choisissez la date de retenue");
	$form = str_replace("##OLIB##", $olib, $form);
	}
	else
	{
	// reproposer le formulaire de choix de type et indiquer l'erreur
	$form .= $this->formulaireChoixTypeRetenue($typeDeRetenue);
	$form .= "<p>Aucune date n'est encore définie pour ce type de retenue.</p>\n";
	}
return $form;
}
//--------------------------------------------------------------------
function selectTypeRetenue ($type=0)
{
// établissement de la liste de sélection à présenter dans le formulaire
// de choix du type de retenue
$options = "<select size=\"1\" name=\"typeDeRetenue\">\n";
foreach ($this->intituleDesRetenues as $uneRetenue)
	{
	$typeDeRetenue = $uneRetenue['typeDeRetenue'];
	$intitule = $uneRetenue['intitule'];
	$options .= "\t<option value='$typeDeRetenue'";
	if ($type == $typeDeRetenue) $options .= " selected";
	$options .= ">$intitule</option>\n";
	}
$options .= "</select>\n";
return $options;
}
//------------------------------------------------------------------------------------
function formulaireChoixTypeRetenue($type=0)
{
// établissement de la liste de sélection à présenter dans le formulaire
// de choix du type de retenue
$options = $this->selectTypeRetenue($type);
$form ="<form name=\"form1\" method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">";
if (utilisateurParmi ("educ", "admin"))
	// le bouton d'ajout d'une nouvelle retenue apparaît seulement pour admin et éducateur
	{
	$form .= "<div style=\"float: right;\"><ul class=\"menuhorz\">\n";
	$form .= "<li>\n<a href=\"retenue.php?mode=nouveau\">Nouvelle retenue</a>\n";
	$form .= "</li>\n</ul>\n</div>\n";
	}
$form .= $options;
$form .= "<a href=\"void(0)\" ##OLIB##>?</a>\n";
$form .= "<input name=\"mode\" value=\"Date\" type=\"submit\">";
$form .= "</form>\n";
$olib = overlib("Choisissez le type de retenue");
$form = str_replace("##OLIB##", $olib, $form);
return $form;
}
}
?>
