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

function lien ($uneLigne)
{
$nom = $uneLigne['nom'];
$prenom = $uneLigne['prenom'];
$ideleve = $uneLigne['ideleve'];
$classe = $uneLigne['classe'];
$texte = "<p><a href=\"ficheel.php?ideleve=$ideleve&mode=voir\" class=\"bulle\">";
$texte .= "<span>Cliquer pour ouvrir la fiche dans une nouvelle fenêtre</span>";
$texte .= "$classe $nom $prenom</a></p>\n";
return $texte;
}

function enteteFaits ($date1, $date2, $classe, $resultat)
{
$texte = "<h3>Synthèse: ";
$texte .= ($date1 != '')?"depuis $date1 ":"";
$texte .= ($date2 != '')?"jusqu'à $date2 ":"";
$texte .= ($classe != '')?":: Classe: $classe":"";
$texte .= "</h3>\n";
return $texte;
}

function syntheseFaits ($date1, $date2, $classe, $resultat)
{
$prototypeFait = new prototypeFait;
$faits = $prototypeFait->tableauTitresFaits();

$texte = enteteFaits ($date1, $date2, $classe, $resultat);
if (count($resultat)>0)
	{
	$n = 0;
	foreach ($resultat as $uneLigne)
		{
		$classe = $uneLigne['classe'];
		$nom = $uneLigne['nom'];
		$prenom = $uneLigne['prenom'];
		$ideleve = $uneLigne['ideleve'];
		$ladate = sh_date_sql_php($uneLigne['ladate']);
		$motif = $uneLigne['motif'];

		$typeFait = $uneLigne['type'];
		$intituleFait = $faits[$typeFait]['intituleFait'];

		$texte .= "<p><span class=\"label\">";
		$texte .= "$ladate</span> <a href=\"ficheel.php?mode=voir";
		$texte .= "&ideleve=$ideleve\" target=\"_blank\" class=\"bulle\">";
		if (strlen($motif) > 0)
			{
			$motif = substr($motif, 0, 100)."...";
			$texte .= "<span class=\"special\">$motif</span>";
			}
			else
			{
			$texte .= "<span>Cliquer pour ouvrir la fiche de l'élève dans une ";
			$texte .= "nouvelle fenêtre.</span>";
			}
		$texte .= "$classe $nom $prenom : $intituleFait";
		$texte .= "</a></p>\n\n";
		$n++;
		}
	$texte .= "Total: $n fait(s).\n";
	}
	else
	$texte .= "<p class=\"impt\">Aucun résultat</p>\n";
return $texte;
}

function titreSectionFaits ($groupeFaits, $description)
{
$intituleFait = $description['titreFait'];
$nombreFaits = count($groupeFaits);
$fragment = "<h3>Type de fait: $intituleFait :: Nombre: $nombreFaits</h3>";
return $fragment;
}

function TitreColonnes ($description, $contexte)
{
$tableau = "<tr>\n";
foreach ($description as $leChamp)
	if (strpos( $leChamp['contextes'], $contexte)!==FALSE)
		$tableau .= "\t<td>{$leChamp['label']}</td>\n";
$tableau .= "</tr>\n";
return $tableau;
}

function ContenuTableau ($unGroupeFaits, $description, $contexte)
{
$fragment = "";
foreach ($unGroupeFaits as $unFait)
	{
	$fragment .= "<tr>\n";
	foreach ($description as $leChamp)
		// ce champ est-il affiché dans ce contexte?
		if (strpos( $leChamp['contextes'], $contexte)!==FALSE)
			{
			$nomChamp = $leChamp['champ'];
			$typeDate = $leChamp['typeDate'];
			$donnee = $unFait[$nomChamp];
			// si c'est une date, convertir de sql vers php
			if ($typeDate) $donnee = sh_date_sql_php($donnee);
			$fragment .="\t<td>$donnee</td>\n";
			}
	$fragment .= "</tr>\n";
	}
return $fragment;
}

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

function lienEleve ($unEleve)
{
$references = referencesEleve($unEleve);
$ideleve = $references['ideleve'];
$nom = $references['nom'];
$prenom = $references['prenom'];
$classe = $references['classe'];
$fragment = "<p>\n<a href=\"ficheel.php?mode=voir&ideleve=$ideleve\" ";
$fragment .= "target=\"_blank\" class=\"bulle\"><span>Cliquer pour ouvrir la fiche de l'élève ";
$fragment .= "dans une nouvelle fenêtre.</span>";
$fragment .= "$classe $nom $prenom</a>\n</p>\n";
return $fragment;
}

function EnteteEleve ($unEleve)
{
$references = referencesEleve ($unEleve);
$nom = $references['nom'];
$prenom = $references['prenom'];
$classe = $references['classe'];
$contrat = $references['contrat'];
$fragment = "<h2>$nom $prenom :: $classe";
if ($contrat == "O")
	$fragment .= " -> CONTRAT DE DISCIPLINE";
$fragment .= "</h2>\n";
return $fragment;
}

function ClotureEleve ()
{
return "<br clear=\"all\" style=\"page-break-before:always\">\n";
}

?>
