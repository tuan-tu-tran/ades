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
class listeFaits {
// liste de faits disciplinaires à traiter pour un éléve
// tableau é deux dimensions, indicé sur le numéro de type de fait
// tous les faits de type "i" sont empilés dans la dimension "i" du tableau
var $liste = array();
// l'identificateur de l'éléve
var $ideleve;
// une liste des id's des types de faits existants dans la fiche disciplinaire
var $idTypeFait = array();
//------------------------------------------------------------------------------------
// constructeur de l'objet
function  __construct ($ideleve)
{
$this->ideleve = $ideleve;

// lire la fiche disciplinaire de l'éléve dont l'id est connu
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "SELECT ades_faits.*, ades_retenues.ladate as dateRetenue, ";
$sql .= "ades_retenues.duree, ades_retenues.heure, ades_retenues.local FROM ades_faits ";
$sql .= "LEFT JOIN ades_retenues on ades_faits.idretenue = ades_retenues.idretenue ";
$sql .= "WHERE ideleve='$ideleve' AND supprime !='O' ORDER BY type, ladate ASC";
// echo $sql;
$resultat = mysql_query ($sql);
mysql_close ($lienDB);
// on établit un tableau de la liste des différents faits disciplinaires
// pour l'éléve courant; cette liste est indicée sur le numéro du type de fait
while ($faitSuivant = mysql_fetch_assoc($resultat))
	{
	$typeFait = $faitSuivant['type'];
	$this->liste[$typeFait][] = $faitSuivant;
	if (!(in_array($typeFait, $this->idTypeFait)))
		$this->idTypeFait[] = $typeFait;
	}
} // listeFaits

function debutTable ()
{
return "\n<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\">\n";
} // debutTable
function finTable ()
{
return "</table>\n</div>\n<br />\n";
} // finTable

function images ($type)
{
switch ($type)
{
case "imgedt":
	$img = "<a href=\"fait.php?mode=editer&ideleve={$this->ideleve}&idfait=##idfait##\">";
	$img .= "<img src=\"images/editer.png\" alt=\"editer\" ";
	$img .= "border=\"0\" title=\"editer\"></a>";
	break;
case "imgsup":
	$img = "<a href=\"fait.php?mode=confirmer&ideleve={$this->ideleve}&idfait=##idfait##\">";
	$img .= "<img src=\"images/suppr.png\" alt=\"supprimer\" ";
	$img .= "border=\"0\" title=\"supprimer\"></a>";
	break;
case "imgimp":
	$img = "<a href=\"imprretenue.php?idfait=##idfait##\" target=\"_blank\">";
	$img .= "<img src=\"images/i.gif\" alt=\"imprimer\" ";
	$img .= "border=\"0\" title=\"imprimer\"></a>";
	break;
}
return $img;
}  // function images

function ecrire ()
{
$contexte = "tableau";
// lecture de tous les types de faits et de leurs caractéristiques
$listeTypeFaits = new typefait();
// on passe chacun des types de faits en revue
foreach ($listeTypeFaits->liste as $leTypeFait)
	{
	// chaque type de fait est caractérisé par un identificateur
	$typeFait = $leTypeFait['id_TypeFait'];
	// on isole une sous-liste des faits de ce type pour l'éléve courant
	$sousListe = $this->liste[$typeFait];
	$nombreFaits = count($sousListe);

	if ($nombreFaits > 0)
		{
		// --------------------------------------------------------
		// on recherche la description du fait en cours
		// dans la liste de description de tous les types de faits, 
		// on ne retient que le fait de type actuel $typeFait
		$tableauEnCours = $listeTypeFaits->descriptionNo($typeFait);
		$couleurFond = $tableauEnCours['couleurFond'];
		$couleurTexte = $tableauEnCours['couleurTexte'];
		$titreFait = $tableauEnCours['titreFait'];
		$typeDeRetenue = $tableauEnCours['typeDeRetenue'];
		// $lesChamps est un tableau de dimension variable en fonction
		// des champs nécessaires é la description du fait (analysé plus loin)
		$lesChamps = $tableauEnCours['champs'];
		// ---------------------------------------------------------

		// ---------------------------------------------------------
		// entéte du fait disciplinaire
		$tableau .= "\n<div style=\"background-color:#$couleurFond;color:#$couleurTexte\">";
		$tableau .= "<strong>$titreFait</strong> nombre : $nombreFaits\n";
		$tableau .= $this->debutTable();
		// colonne pour les icénes editer, supprimer, imprimer
		$tableau .= "<tr>\n\t<td width=\"48\">&nbsp;</td>\n";

		foreach ($lesChamps as $leChamp)
			{
			// Vérifier que le champ est visible dans le $contexte
			if (strpos( $leChamp['contextes'], $contexte)!==FALSE)
				$tableau .= "\t<td>{$leChamp['label']}</td>\n";
			}
		// on cléture la ligne de titre
		$tableau .= "</tr>\n";
		// fin de l'entéte ----------------------------------------

		// description du ou des faits correspondant é l'entéte
		foreach ($sousListe as $faitSuivant)
			{
			$nofait = (string) $faitSuivant['idfait'];
			$img1 = str_replace('##idfait##', $nofait, $this->images('imgedt'));
			$img2 = str_replace('##idfait##', $nofait, $this->images('imgsup'));
			$img3 = str_replace('##idfait##', $nofait, $this->images('imgimp'));
	
			$tableau .= "<tr>\n";
			$images = $img1.$img2;
			if ($typeDeRetenue) $images .= $img3;
			$tableau .= "\n<td>$images</td>\n";
	
			// détail de chacune des colonnes du tableau
			foreach ($lesChamps as $leChamp)
				// si le champ $leChamp doit apparaétre dans le contexte défini
				if (strpos( $leChamp['contextes'], $contexte)!==FALSE)
					{
					$nomChamp = $leChamp['champ'];
					$typeDate = $leChamp['typeDate'];
					$donnee = $faitSuivant[$nomChamp];

					// si c'est une date, convertir de sql vers php
					if ($typeDate) $donnee = sh_date_sql_php($donnee);
					$tableau .="\t<td>$donnee</td>\n";
					}
			// cléturer la ligne du tableau en cours
			$tableau .= "</tr>\n";
			}
		$tableau .= $this->finTable();
		}
	}
return $tableau;
}


}
?>
