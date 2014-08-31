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
require ("inc/classes/classlisteretenues.inc.php");

use EducAction\AdesBundle\View;
use EducAction\AdesBundle\Label;

// cette classe gère les faits disciplinaires
// la table $listeRubriques contient les différentes caractéristiques du fait:
// idfait, idorigine, ideleve, type, date, prof, sanction,...
// cette liste contient des mentions fixes et obligatoires (idfait, ideleve,...)
// et des mentions caractéristiques du fait courant (idretenue, sanction,...)
// La liste des mentions est gérée par l'objet typeFait (classtypefait.inc.php)

class fait {
var $listeRubriques = array();

// constructeur de la classe
function __construct ($idfait='-1',$type='-1',$ideleve='-1')
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
if ($idfait ==-1)
	{
	// c'est un nouveau fait
	$this->setRubrique ('idfait', $idfait);
	$this->setRubrique ('type', $type);
	$this->setRubrique ('ideleve', $ideleve);
	// simultation de date MySQL
	$this->setRubrique ('ladate', date("Y-m-d"));
	$this->setRubrique ('qui', $_SESSION["identification"]["idedu"]);
	}
	else
	{
	// c'est un fait à relire dans la BD
	$this->lirefait($idfait);
	// on a besoin de connaître le type de fait pour déterminer le type éventuel de retenue
	$type = $this->getRubrique('type');	
	}

// déterminer le type de retenue correspondant, si le cas est échéant
// possible uniquement si l'on connaît déjà le type du fait
if ($type > 0)
	{
	$typeFait = new prototypeFait();
	$typeDeRetenue = $typeFait->typeRetenueFaitId($type);
	// si c'est une retenue, on retient à quel type elle appartient
	if ($typeDeRetenue > 0)
		$this->setRubrique('typeDeRetenue', $typeDeRetenue);
	}
mysql_close ($lienDB);
return true;
}

//------------------------------------------------------------------------------------
// lecture du fait $idFait dans la BD
function lirefait($idFait) 
{

$sql = "SELECT * FROM ades_faits WHERE idfait='$idFait'";
// echo $sql;
$resultat = mysql_query ($sql);

$this->listeRubriques = mysql_fetch_assoc($resultat);
// à toutes fins utiles, se souvenir du "idretenue" éventuel
$this->ancienidRetenue = $this->getRubrique('idretenue');
return true;
}

//------------------------------------------------------------------------------------
// modifie le contenu d'un champ du fait
function setRubrique ($rubrique, $donnee)
{
$this->listeRubriques[$rubrique] = $donnee;
// marquer le fait comme modifié ce jour
$this->listeRubriques['dermodif'] = date("Y-m-d");
return true;
}

//------------------------------------------------------------------------------------
// lit le contenu d'un champ du fait
function getRubrique ($rubrique)
{
return ($this->listeRubriques[$rubrique]);
}

//------------------------------------------------------------------------------------
// enregistre le fait courant dans la BD
function enregistrer ()
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);

// si le fait est en édition (dans ce cas, il possède un 'idfait' significatif
$idfait = $this->getRubrique('idfait');
$ideleve = $this->getRubrique('ideleve');

// $idfait = -1 s'il s'agit d'un nouveau fait (pas encore identifié dans la BD)
if (!($idfait == -1))
	{
	// C'est un fait existant; nous sommes dans le cas de l'édition
	// marquer le fait comme supprimé dans la BD;
	// il sera réenregistré avec un nouveau "idfait"
	$sql = "UPDATE ades_faits SET supprime = 'O' WHERE idfait ='$idfait'";
	// echo "$sql >br />";
	$resultat = mysql_query($sql);
	// indiquer le 'idorigine' comme étant 'idfait' de manière à pouvoir
	// retrouver le précédent enregistrement du fait dans la BD
	$this->setRubrique('idorigine', $this->getRubrique('idfait'));
	}
// puis enregistrer un nouveau fait possédant idorigine identique
// et marqué comme modifié par l'utilisateur actuel
$this->setRubrique('qui', $_SESSION["identification"]["idedu"]);
$sql = $this->formeSQL();

$resultat = mysql_query($sql) or die("Erreur lors de l'enregistrement");

// si c'est une retenue, ajuster les nombres d'inscrits aux retenues
$idretenue = $this->getRubrique('idretenue');
if (isset($idretenue)) $this->ajusterRetenues();

mysql_close($lienDB);
redir ("ficheel.php", "mode=voir&ideleve=$ideleve", "Enregistrement effectué");
return $resultat;
}

//------------------------------------------------------------------------------------
// formeSQL sert à former la requête SQL qui permet l'enregistrement
function formeSQL()
{
$sql = "INSERT INTO ades_faits SET ";
$nb = count($this->listeRubriques);
$i = 0;
foreach ($this->listeRubriques as $key=>$value)
	{
	$i++;
	// le champ "idfait" est auto-incrémenté dans la BD.
	// il ne faut donc pas en tenir compte
	if ($key != 'idfait')
		{
		$value = mysql_real_escape_string($value);
		$sql .= "$key = '$value'";
		if ($i < $nb) $sql .= ", ";
		}
	}
return $sql;
}

function confirmeSuppression ($ideleve)
{
$id_TypeFait = $this->getRubrique('type');
$idfait = $this->getRubrique('idfait');

// recherche des caractéristiques des rubriques à faire figurer dans le formulaire
$prototypeFaits = new prototypeFait();
$faitATraiter = $prototypeFaits->descriptionFaitId($id_TypeFait);

$couleurFond = $faitATraiter['couleurFond'];
$couleurTexte = $faitATraiter['couleurTexte'];
$titreFait = $faitATraiter['titreFait'];

$form  = "<h2>Veuillez confirmer la suppression du fait suivant</h2>\n";
$form .= "<h3 style=\"background-color: ###COULEURFOND##; color: ###COULEURTEXTE##\">##TITRE##</h3>\n";

$descriptionChamps = $prototypeFaits->detailDesChampsPourContexte($id_TypeFait,'minimum');
// formulaire de confirmation de la suppression
$form .= "<form name=\"form1\" method=\"post\" action=\"{$_SERVER[PHP_SELF]}\">\n";

foreach ($descriptionChamps as $unChamp)
	{
	// pour le champ en cours, recherche de ses caractéristiques
	foreach ($unChamp as $key => $value)
		$key = $value;
	// $typeDate, $label figurent dans les valeurs de $key
	$valeur = $this->getRubrique($Champ);
	if ($typeDate)
		$valeur = sh_date_sql_php($valeur);
	$form .= "<p><span class=\"label\">$label :</span>$valeur</p>\n";
	}
// s'il s'agit d'une retenue, on présente ses caractéristiques principales
$idretenue = $this->listeRubriques['idretenue'];
if ($idretenue > 0)
	{
	require ("inc/classes/classretenue.inc.php");
	$retenue = new retenue($idretenue);
	$intitule = $retenue->intitule();
	$form .= "<p><span class=\"label\">Retenue</span>$intitule</p>\n";
	}

$form .= "<input type=\"hidden\" name=\"idfait\" value=\"##IDFAIT##\">\n";
$form .= "<input type=\"hidden\" name=\"ideleve\" value=\"##IDELEVE##\">\n";
$form .= "<div style=\"text-align:center\">\n";
$form .= "<input type=\"submit\" name=\"mode\" value=\"Supprimer\">\n";
$form .= "<input type=\"reset\" name=\"submit\" value=\"Annuler\"";
$form .= "onclick=\"javascript:history.go(-1)\">\n";
$form .= "</div>\n</form>\n";

$form = str_replace ("##COULEURFOND##", $couleurFond, $form);
$form = str_replace ("##COULEURTEXTE##", $couleurTexte, $form);
$form = str_replace ("##TITRE##", $titreFait, $form);
$form = str_replace ("##IDFAIT##", $idfait, $form);
$form = str_replace ("##IDELEVE##", $ideleve, $form);

return $form;
}

//------------------------------------------------------------------------------------
// marquer le fait $idfait comme supprimé dans la BD
function supprimer($ideleve)
{
$idfait = $this->getRubrique('idfait');
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "UPDATE ades_faits SET supprime = 'O' WHERE idfait ='$idfait'";
// echo "$sql <br />";
$resultat = mysql_query($sql);

// ajuster les inscriptions aux différentes retenues
// méthode barbare et brutale pour éviter les chipotages
$this->ajusterRetenues();

redir ("ficheel.php", "mode=voir&ideleve=$ideleve", "Suppression du fait effectuée");
return resultat;
}

//------------------------------------------------------------------------------------
// lit vers $listeRubriques le contenu du formulaire
function ramassePost ($post)
{
$contexte = 'formulaire';
// quel est le type du fait? On le trouve dans le formulaire
$id_TypeFait = $post['type'];
$prototypeFaits = new prototypeFait();
$faitATraiter = $prototypeFaits->descriptionFaitId($id_TypeFait);

// recherche de la liste de description de chaque champ
$descriptionChamps = $prototypeFaits->detailDesChampsPourContexte($id_TypeFait,'formulaire');

foreach ($descriptionChamps as $unChamp)
	{
	$nomChamp = $unChamp['champ'];
	$valeur = $post[$nomChamp];
	// est-ce une date? Alors transformer vers le format MYSQL
	if ($unChamp['typeDate'])
		$valeur = date_php_sql($valeur);
	$this->setRubrique ($nomChamp, $valeur);
	}
}

//------------------------------------------------------------------------------------
// compose le formulaire correspondant au type de fait actuel
function formulaire ($ideleve)
{
$contexte = 'formulaire';
// quel est le type du fait?
$type = $this->getRubrique('type');
// recherche des caractéristiques des rubriques à faire figurer dans le formulaire
$listeTypeFaits = new prototypeFait();
$faitATraiter = $listeTypeFaits->descriptionFaitId($type);

$couleurFond = $faitATraiter['couleurFond'];
$couleurTexte = $faitATraiter['couleurTexte'];
$titreFait = $faitATraiter['titreFait'];
$focus = $faitATraiter['focus'];

$form = "<h3 style=\"background-color: #$couleurFond; color: #$couleurTexte\">";
$form .= "$titreFait</h3>\n";
$form .= "<form name=\"form1\" method=\"post\" action=\"{$_SERVER[PHP_SELF]}\"";
$form .= " onsubmit=\"return(verifForm(this))\">\n";

// recherche de la liste de description de chaque champ
// qui doit apparaître dans le formulaire
$descriptionChamps = $listeTypeFaits->detailDesChampsPourContexte($type,"formulaire");

foreach ($descriptionChamps as $unChamp)
	{
	// on vérifie si le champ doit être affichée dans le $contexte
	// les $contexte(s) sont précisés dans le fichier .ini de la description des champs
	if (strpos( $unChamp['contextes'], $contexte)!==FALSE)
		{
		// pour le champ en cours, recherche de ses caractéristiques
		foreach ($unChamp as $key => $value)
			$$key = $value;
		if ($javascriptEvent != "")
			$javascript = "$javascriptEvent=\"$javascriptCommand\"";
			else $javascript ="";
		// la variable $champ fait partie des caractéristiques obligatoire (provenant de $$key)
		$valeur = $this->getRubrique($champ);

		// la variable $typeChamp fait partie des caractéristiques obligatoires (provenant de $$key)
		switch ($typeChamp)
			{
			case 'text':
			// s'il s'agit d'une date, convertir de la notation MySQL vers la notation PHP
			if ($typeDate)
				$valeur = sh_date_sql_php ($valeur);
			
			$form .= "\t<p><label for=\"$champ\">$label </label>\n";
			$form .= "\t<input name=\"$champ\" id=\"$champ\" value=\"$valeur";
			$form .= "\" type=\"text\" size=\"$size\" maxlength=\"$maxlength\" ";
			$form .= "class=\"$classCSS\" $javascript>\n";
			if ($typeDate)
				$form .= "\t<span id=\"calendrier\" style=\"position: absolute; z-index: 100;\"></span>";
			$form .= "</p>\n";
			break;
			case 'textarea':
			$form .= "\t<p><label for=\"$champ\">$label </label>\n";
			$form .= "\t<textarea cols=\"$colonnes\" rows=\"$lignes\" name=\"$champ\" id=\"$champ\"";
			$form .= " class=\"$classCSS\" $javascript>";
			$form .= "$valeur</textarea></p>\n";
			break;
			case 'hidden':
			$form .= "\t<input name=\"$champ\" value=\"$valeur\"";
			$form .= " type=\"hidden\">\n";
			break;
			case 'select':
			$form .= "\t<p><label for=\"$champ\">$label </label>\n";
			$form .= "\t<select name=\"$champ\" id=\"champ\">\n";
			// la liste des options est est à préparer séparément et à insérer à la place
			// du motif ##options##
			$form .= "\t##options##\n";
			$form .= "\t</select>\n";

			// préparation des options s'il s'agit d'un champ "Date de retenue",
			if ($typeDateRetenue)
				{
				// on recherche la liste des dates de retenues pour ce type, dans la BD
				$listeRetenues = new listesDeRetenues();
				// le type de retenue est déterminé dans le constructeur
				$typeDeRetenue = $this->getRubrique('typeDeRetenue');
				$listeOptions = $listeRetenues->listeOptions($typeDeRetenue, $valeur, true);

				// prévoir le cas où aucune date de retenue n'est définie
				if (strlen($listeOptions) == 0)
					{
					redir ("ficheel.php", "mode=voir&ideleve=$ideleve",
					"Aucune date de retenue n'est encore d&eacute;finie \nou elles sont toutes cach&eacute;es.",3000);
					exit;
					}
				$form = str_replace("##options##",$listeOptions,$form); 
				}
			break;
			}
		}
	}
$labels=Label::GetForFact($this->getRubrique("idfait"));
$allLabels = Label::GetAll();
$form .= View::GetHtml("label_edit.inc.php", array(
    "currentLabels"=> $labels
    , "allLabels" => $allLabels
));
$form .= "<div style=\"text-align:center\">\n";
$form .= "<input type=\"submit\" name=\"mode\" value=\"Enregistrer\">\n";
$form .= "<input type=\"reset\" name=\"submit\" value=\"R&eacute;initialiser\">\n</div>\n";
$form .= "</form>\n";
if ($focus != '')
	$form .= selectChampFormulaire($focus); 
return $form;
}

// ajustement du nombre d'inscrits à chaque retenue avec le contenu de la table des
// faits (ades_faits). Le nombre d'inscrits est compté dans ades_faits et mis à jour
// dans ades_retenues
// Méthode bête et brutale, mais qui fonctionne tout en suffisamment légère
// maximum deux requêtes de mise à jour sur la BD après une modification de retenues

// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//
// ne remet pas à zéro les retenues qui n'ont plus aucun inscrit!!      //
// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//

function ajusterRetenues ()
{
// la requête suivante peut être simplifiée pour l'usage présent
$sql = "SELECT ades_faits.idretenue, COUNT(*) as occupationReelle, places, ";
$sql .= "occupation, ades_retenues.ladate as dateRetenue, heure, local, duree ";
$sql .= "FROM ades_faits ";
$sql .= "LEFT JOIN ades_retenues ON ades_retenues.idretenue = ades_faits.idretenue ";
$sql .= "WHERE  supprime !='O' AND ades_faits.idretenue > 0 ";
$sql .= "GROUP BY ades_faits.idretenue";
// echo $sql;

$resultat = mysql_query($sql);
// on dispose de la liste des retenues avec leurs occupations réelles recalculées
// sur la base des inscriptions dans la table des faits. On peut donc ajuster les nombres
// d'inscrits indiqués dans la table des retenues
while ($uneRetenue = mysql_fetch_assoc($resultat))
	{
	$occupationReelle = $uneRetenue['occupationReelle'];
	$occupation = $uneRetenue['occupation'];
	if ($occupation != $occupationReelle)
		{
		$idretenue = $uneRetenue['idretenue'];
		$sql = "UPDATE ades_retenues SET occupation = '$occupationReelle' ";
		$sql .= "WHERE idretenue = '$idretenue'";
		// echo "$sql <br />";
		$updtate = mysql_query($sql);
		}
	}
}

}
?>
