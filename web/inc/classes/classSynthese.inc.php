<?php
/**
 * Copyright (c) 2014 Educ-Action
 * Copyright (c) 2015 Tuan-Tu Tran
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
require ("inc/classes/classDescriptionFait.inc.php");
require ("inc/funcdate.inc.php");
require dirname(__FILE__)."/../../../vendor/autoload.php";
// classe regroupant l'ensemble des fonctions nécessaires pour
// les différentes synthèses

use EducAction\AdesBundle\Config;

class synthese {

function __construct ()
{
// vide pour l'instant
}

function titreNomPrenomClasse ($identite)
{
$texte = "<h3>Nom : <strong>##NOM## ##PRENOM##</strong> :: ";
$texte .= "Classe : <strong>##CLASSE##</strong>\n";
$texte .= "<span class=\"impt\">##CONTRAT##</span></h3>\n";
$contrat = $identite['contrat']=="O"?" :: Contrat":"";
$texte = str_replace("##NOM##", $identite['nom'], $texte);
$texte = str_replace("##PRENOM##", $identite['prenom'], $texte);
$texte = str_replace("##CLASSE##", $identite['classe'], $texte);
$texte = str_replace("##CONTRAT##", $contrat, $texte);
return $texte;
}

function lienVersEleve ($identite)
{
$texte = "<p><a href=\"ficheel.php?ideleve=##IDELEVE##&amp;mode=voir\" target=\"_blank\" ";
$texte .= "onmouseover=\"return overlib('Cliquer pour ouvrir la fiche dans une nouvelle fenêtre')\" ";
$texte .= "onmouseout=\"return nd();\">";
$texte .= "##CLASSE## ##NOM## ##PRENOM##</a></p>\n";
$texte = str_replace("##NOM##", $identite['nom'], $texte);
$texte = str_replace("##PRENOM##", $identite['prenom'], $texte);
$texte = str_replace("##CLASSE##", $identite['classe'], $texte);
$texte = str_replace("##IDELEVE##", $identite['ideleve'], $texte);
return $texte;
}

// $unEleve contient l'ensemble des faits disciplinaires
// pour un élève
function referencesEleve ($unEleve)
{
$references = array();
$eleve = current($unEleve);
$references['nom'] = $eleve[0]['nom'];
$references['prenom'] = $eleve[0]['prenom'];
$references['classe'] = $eleve[0]['classe'];
$references['contrat'] = $eleve[0]['contrat'];
$references['ideleve'] = $eleve[0]['ideleve'];
return $references;
}

function ClotureEleve()
{
return "<br clear=\"all\" style=\"page-break-before:always\">\n";
}

function requeteSynthese ($post)
{
foreach ($post as $key => $value)
	$$key = $value;

$date1 = ($date1 !="")? date_php_sql($date1):'';
$date2 = ($date2 !="")? date_php_sql($date2):'';
	
$sql = "SELECT ades_eleves.nom, ades_eleves.prenom, ades_eleves.classe, ades_eleves.contrat, ";
$sql .= "ades_faits.*, ades_retenues.ladate as dateRetenue, ades_retenues.local, ";
$sql .= "ades_retenues.duree, ades_retenues.heure ";
$sql .= "FROM (ades_faits, ades_eleves) ";
$sql .= "LEFT JOIN ades_retenues ON ades_faits.idretenue = ades_retenues.idretenue ";
$sql .= "WHERE ";
$sql .= ($date1 != '')?"ades_faits.ladate >= '$date1'":"1 ";
$sql .= " AND ";
$sql .= ($date2 != '')?"ades_faits.ladate <= '$date2'":"1 ";
$sql .= " AND ";
$sql .= ($classe != '')?"classe LIKE '$classe%' ":"1 ";
$sql .= "AND ades_eleves.ideleve = ades_faits.ideleve ";
$sql .= "AND ades_faits.supprime !='O' ";
$sql .= "ORDER BY ades_eleves.classe, ades_eleves.nom, ades_eleves.prenom, ";
$sql .= "ades_eleves.ideleve, ades_faits.type";
// echo $sql;
include ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$resultat = mysql_query ($sql);

while ($uneLigne = mysql_fetch_assoc($resultat))
	$lignes[] = $uneLigne;
return $lignes;
}

function enteteFaits ($date1, $date2, $classe)
{
$texte = "<h3>Synthèse: ";
$texte .= ($date1 != '')?"depuis $date1 ":"";
$texte .= ($date2 != '')?"jusqu'à $date2 ":"";
$texte .= ($classe != '')?":: Classe: $classe":"";
$texte .= "</h3>\n";
return $texte;
}

function syntheseEleves ($date1, $date2, $classe, $resultat)
{
$prototype = new prototypeFait();
// initialisation de la page HTML externe pour impression
$pageImpression = "";
// initialisation de la liste des liens vers les fiches individuelles

$lignesLiens = $this->enteteFaits ($date1, $date2, $classe);;

$listeFaitsParEleve = array();
// regroupement par élèves et par type de faits
foreach ($resultat as $uneLigne)
	{
	$ideleve = $uneLigne['ideleve'];
	$id_TypeFait = $uneLigne['type'];
	// recherche des informations individuelles
	$listeFaitsParEleve[$ideleve]['identite']['ideleve']=$ideleve;
	$listeFaitsParEleve[$ideleve]['identite']['nom']=$uneLigne['nom'];
	$listeFaitsParEleve[$ideleve]['identite']['prenom']=$uneLigne['prenom'];
	$listeFaitsParEleve[$ideleve]['identite']['classe']=$uneLigne['classe'];
	$listeFaitsParEleve[$ideleve]['identite']['contrat']=$uneLigne['contrat'];
	// on ne conserve que la partie résiduelle de la description du fait
	$listeFaitsParEleve[$ideleve]['faits'][$id_TypeFait][] = array_slice($uneLigne,4,count($uneLigne));
	}
// ($listeFaitsParEleve, true);
foreach ($listeFaitsParEleve as $ideleve=>$detailFaitPourEleve)
	{
	// on sépare l'identité de l'élève de la description des faits disciplinaires
	$identite = $detailFaitPourEleve['identite'];
	$faits = $detailFaitPourEleve['faits'];
	// afficher ($identite); afficher ($faits);
	// En haut de chaque page, impression des données d'identité de l'élève
	$pageImpression .= $this->titreNomPrenomClasse($identite);
	// et on prépare la ligne à imprimer à l'écran
	$lignesLiens .= $this->lienVersEleve($identite);
	// impression du détail des faits
	foreach ($faits as $id_TypeFait=>$listeFaits)
		{
		$nombre = count($listeFaits);
		// on traite le groupe de type $id_TypeFait
		$titreTable = "<h3>{$prototype->titreFaitId($id_TypeFait)} || Nombre: $nombre</h3>\n";
		$titreTable .= "<table width=\"100%\" border=\"1px\">\n";
		// demander au prototype les titres des colonnes de la table pour ce fait
		$titreTable .= $prototype->htmlTitreColonnesTableau ($id_TypeFait, false);
		$pageImpression .= $titreTable;
		// traitement de tous les faits de ce type
		foreach ($listeFaits as $unFait)
			{
			// afficher ($unFait);
			// demander au prototype le modèle des colonnes pour ce fait
			$nouvelleLigne = $prototype->htmlChampsTableau ($id_TypeFait, false);
			$lesChamps = $prototype->detailDesChampsPourContexte ($id_TypeFait, 'tableau');
			foreach ($lesChamps as $unChamp)
				{
				$nomChamp = $unChamp['champ'];
				if ($unChamp['typeDate'])
				$unFait[$nomChamp] = sh_date_sql_php($unFait[$nomChamp]);
				// remplacer les ##machin## par la valeur des champs correspondants
				$nouvelleLigne = str_replace ("##$nomChamp##", stripslashes($unFait[$nomChamp]), $nouvelleLigne);
				}
			$pageImpression .= $nouvelleLigne;
			}
		$pageImpression .= "</table>\n";
		// fin du tableau pour un type de fait
		}
	$pageImpression .= $this->ClotureEleve();
	}
if (!($fp=fopen(Config::LocalFile("synthese.html"), "w"))) die ("Impossible d'ouvrir le fichier");
fwrite ($fp, $pageImpression);
fclose ($fp);
echo telecharger ("synthese.html");
return $lignesLiens;
}

function syntheseFaits ($date1, $date2, $classe, $resultat)
{
$prototypeFait = new prototypeFait;
$faits = $prototypeFait->tableauTitresFaits();
$texte = $this->enteteFaits ($date1, $date2, $classe);
$nombreFaits = count($resultat);
if ($nombreFaits > 0)
	{
	foreach ($resultat as $uneLigne)
		{
		$classe = $uneLigne['classe'];
		$nom = $uneLigne['nom'];
		$prenom = $uneLigne['prenom'];
		$ideleve = $uneLigne['ideleve'];
		$ladate = sh_date_sql_php($uneLigne['ladate']);
		$motif = $uneLigne['motif'];

		$typeFait = $uneLigne['type'];
		$intitule = $prototypeFait->titreFaitId($typeFait);

		$texte .= "<p><span class=\"label\">##LADATE##</span>";
		$texte .= "<a href=\"ficheel.php?mode=voir&amp;ideleve=##IDELEVE##\" ";
		$texte .= "target=\"_blank\" ##OVERLIB##>";
		if ($motif=="")
			$motif = "Cliquer pour ouvrir la fiche de l'élève dans une nouvelle fenêtre.";
		$olib = overlib($motif);
		$texte .= "##CLASSE## ##NOM## ##PRENOM##: <strong>##INTITULE##</strong></a></p>\n\n";

		$texte = str_replace ("##OVERLIB##", $olib, $texte);
		$texte = str_replace ("##LADATE##", $ladate, $texte);
		$texte = str_replace ("##IDELEVE##", $ideleve, $texte);
		$texte = str_replace ("##CLASSE##", $classe, $texte);
		$texte = str_replace ("##NOM##", $nom, $texte);
		$texte = str_replace ("##PRENOM##", $prenom, $texte);
		$texte = str_replace ("##INTITULE##", $intitule, $texte);
		}
	$texte .= "Total: $nombreFaits fait(s).\n";
	}
	else
	$texte .= "<p class=\"impt\">Aucun résultat</p>\n";
return $texte;
}

}
?>
