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

// cette classe g�re les faits disciplinaires
// la table $listeRubriques contient les diff�rentes caract�ristiques du fait:
// idfait, idorigine, ideleve, type, date, prof, sanction,...
// cette liste contient des mentions fixes et obligatoires (idfait, ideleve,...)
// et des mentions caract�ristiques du fait courant (idretenue, sanction,...)
// La liste des mentions est g�r�e par l'objet typeFait (classtypefait.inc.php)

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
	// c'est un fait � relire dans la BD
	$this->lirefait($idfait);
	// on a besoin de conna�tre le type de fait pour d�terminer le type �ventuel de retenue
	$type = $this->getRubrique('type');	
	}

// d�terminer le type de retenue correspondant, si le cas est �ch�ant
// possible uniquement si l'on conna�t d�j� le type du fait
if ($type > 0)
	{
	$typeFait = new prototypeFait();
	$typeDeRetenue = $typeFait->typeRetenueFaitId($type);
	// si c'est une retenue, on retient � quel type elle appartient
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
// � toutes fins utiles, se souvenir du "idretenue" �ventuel
$this->ancienidRetenue = $this->getRubrique('idretenue');
return true;
}

//------------------------------------------------------------------------------------
// modifie le contenu d'un champ du fait
function setRubrique ($rubrique, $donnee)
{
$this->listeRubriques[$rubrique] = $donnee;
// marquer le fait comme modifi� ce jour
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

// si le fait est en �dition (dans ce cas, il poss�de un 'idfait' significatif
$idfait = $this->getRubrique('idfait');
$ideleve = $this->getRubrique('ideleve');

// $idfait = -1 s'il s'agit d'un nouveau fait (pas encore identifi� dans la BD)
if (!($idfait == -1))
	{
	// C'est un fait existant; nous sommes dans le cas de l'�dition
	// marquer le fait comme supprim� dans la BD;
	// il sera r�enregistr� avec un nouveau "idfait"
	$sql = "UPDATE ades_faits SET supprime = 'O' WHERE idfait ='$idfait'";
	// echo "$sql >br />";
	$resultat = mysql_query($sql);
	// indiquer le 'idorigine' comme �tant 'idfait' de mani�re � pouvoir
	// retrouver le pr�c�dent enregistrement du fait dans la BD
	$this->setRubrique('idorigine', $this->getRubrique('idfait'));
	}
// puis enregistrer un nouveau fait poss�dant idorigine identique
// et marqu� comme modifi� par l'utilisateur actuel
$this->setRubrique('qui', $_SESSION["identification"]["idedu"]);
$sql = $this->formeSQL();

$resultat = mysql_query($sql) or die("Erreur lors de l'enregistrement");

// si c'est une retenue, ajuster les nombres d'inscrits aux retenues
$idretenue = $this->getRubrique('idretenue');
if (isset($idretenue)) $this->ajusterRetenues();

mysql_close($lienDB);
redir ("ficheel.php", "mode=voir&ideleve=$ideleve", "Enregistrement effectu�");
return $resultat;
}

//------------------------------------------------------------------------------------
// formeSQL sert � former la requ�te SQL qui permet l'enregistrement
function formeSQL()
{
$sql = "INSERT INTO ades_faits SET ";
$nb = count($this->listeRubriques);
$i = 0;
foreach ($this->listeRubriques as $key=>$value)
	{
	$i++;
	// le champ "idfait" est auto-incr�ment� dans la BD.
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

// recherche des caract�ristiques des rubriques � faire figurer dans le formulaire
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
	// pour le champ en cours, recherche de ses caract�ristiques
	foreach ($unChamp as $key => $value)
		$key = $value;
	// $typeDate, $label figurent dans les valeurs de $key
	$valeur = $this->getRubrique($Champ);
	if ($typeDate)
		$valeur = sh_date_sql_php($valeur);
	$form .= "<p><span class=\"label\">$label :</span>$valeur</p>\n";
	}
// s'il s'agit d'une retenue, on pr�sente ses caract�ristiques principales
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
// marquer le fait $idfait comme supprim� dans la BD
function supprimer($ideleve)
{
$idfait = $this->getRubrique('idfait');
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "UPDATE ades_faits SET supprime = 'O' WHERE idfait ='$idfait'";
// echo "$sql <br />";
$resultat = mysql_query($sql);

// ajuster les inscriptions aux diff�rentes retenues
// m�thode barbare et brutale pour �viter les chipotages
$this->ajusterRetenues();

redir ("ficheel.php", "mode=voir&ideleve=$ideleve", "Suppression du fait effectu�e");
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
// recherche des caract�ristiques des rubriques � faire figurer dans le formulaire
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
// qui doit appara�tre dans le formulaire
$descriptionChamps = $listeTypeFaits->detailDesChampsPourContexte($type,"formulaire");

foreach ($descriptionChamps as $unChamp)
	{
	// on v�rifie si le champ doit �tre affich�e dans le $contexte
	// les $contexte(s) sont pr�cis�s dans le fichier .ini de la description des champs
	if (ereg($contexte, $unChamp['contextes']))
		{
		// pour le champ en cours, recherche de ses caract�ristiques
		foreach ($unChamp as $key => $value)
			$$key = $value;
		if ($javascriptEvent != "")
			$javascript = "$javascriptEvent=\"$javascriptCommand\"";
			else $javascript ="";
		// la variable $champ fait partie des caract�ristiques obligatoire (provenant de $$key)
		$valeur = $this->getRubrique($champ);

		// la variable $typeChamp fait partie des caract�ristiques obligatoires (provenant de $$key)
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
			// la liste des options est est � pr�parer s�par�ment et � ins�rer � la place
			// du motif ##options##
			$form .= "\t##options##\n";
			$form .= "\t</select>\n";

			// pr�paration des options s'il s'agit d'un champ "Date de retenue",
			if ($typeDateRetenue)
				{
				// on recherche la liste des dates de retenues pour ce type, dans la BD
				$listeRetenues = new listesDeRetenues();
				// le type de retenue est d�termin� dans le constructeur
				$typeDeRetenue = $this->getRubrique('typeDeRetenue');
				$listeOptions = $listeRetenues->listeOptions($typeDeRetenue, $valeur, true);

				// pr�voir le cas o� aucune date de retenue n'est d�finie
				if (strlen($listeOptions) == 0)
					{
					redir ("ficheel.php", "mode=voir&ideleve=$ideleve",
					"Aucune date de retenue n'est encore d&eacute;finie \nou elles sont toutes cach&eacute;es.",3000);
					exit;
					}
				$form = ereg_replace("##options##",$listeOptions,$form); 
				}
			break;
			}
		}
	}
$form .= "<div style=\"text-align:center\">\n";
$form .= "<input type=\"submit\" name=\"mode\" value=\"Enregistrer\">\n";
$form .= "<input type=\"reset\" name=\"submit\" value=\"R&eacute;initialiser\">\n</div>\n";
$form .= "</form>\n";
if ($focus != '')
	$form .= selectChampFormulaire($focus); 
return $form;
}

// ajustement du nombre d'inscrits � chaque retenue avec le contenu de la table des
// faits (ades_faits). Le nombre d'inscrits est compt� dans ades_faits et mis � jour
// dans ades_retenues
// M�thode b�te et brutale, mais qui fonctionne tout en suffisamment l�g�re
// maximum deux requ�tes de mise � jour sur la BD apr�s une modification de retenues

// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//
// ne remet pas � z�ro les retenues qui n'ont plus aucun inscrit!!      //
// ---------------------------------------------------------------------//
// ---------------------------------------------------------------------//

function ajusterRetenues ()
{
// la requ�te suivante peut �tre simplifi�e pour l'usage pr�sent
$sql = "SELECT ades_faits.idretenue, COUNT(*) as occupationReelle, places, ";
$sql .= "occupation, ades_retenues.ladate as dateRetenue, heure, local, duree ";
$sql .= "FROM ades_faits ";
$sql .= "LEFT JOIN ades_retenues ON ades_retenues.idretenue = ades_faits.idretenue ";
$sql .= "WHERE  supprime !='O' AND ades_faits.idretenue > 0 ";
$sql .= "GROUP BY ades_faits.idretenue";
// echo $sql;

$resultat = mysql_query($sql);
// on dispose de la liste des retenues avec leurs occupations r�elles recalcul�es
// sur la base des inscriptions dans la table des faits. On peut donc ajuster les nombres
// d'inscrits indiqu�s dans la table des retenues
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
