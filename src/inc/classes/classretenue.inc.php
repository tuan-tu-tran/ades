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
class retenue {
var $caracteristiques = array();

// constructeur
function __construct ($idretenue=-1)
{
if ($idretenue !=-1)
	$this->lireretenue($idretenue);
	else
	{
	// donner des valeurs par défaut
	$this->setCaracteristique ('idretenue',-1);
	$this->setCaracteristique ('type',0);
	$this->setCaracteristique ('occupation',0);
	$this->setCaracteristique ('heure','14h00');
	$this->setCaracteristique ('duree','1h');
	$this->setCaracteristique ('affiche','O');
	}
}

function setCaracteristique ($key, $value)
{ $this->caracteristiques[$key] = $value; }

function getCaracteristique ($key)
{ return $this->caracteristiques[$key]; }

function complet()
{return ($this->caracteristiques['occupation'] >= $this->caracteristiques['places']);}

function intitule()
{
$texte = $this->getCaracteristique('duree')." h le ";
$ladate = $this->getCaracteristique('ladate');
$ladate = date_sql_php($ladate);
$texte .= allonger($ladate, 12);
$texte .= " à ".$this->getCaracteristique('heure');
return $texte;
}

function compterRetenue ($idretenue, $increment)
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "UPDATE ades_retenues SET occupation = occupation + $increment ";
$sql = "WHERE idretenue = $idretenue";
$resultat = mysql_query($sql);
mysql_close($lienDB);
}

function enregistrer($formulaire)
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);

foreach ($formulaire as $key=>$value)
	$$key = mysql_real_escape_string($value);
// $idretenue = -1 s'il s'agit d'une nouvelle retenue
if ($idretenue != -1)
	$sql = "UPDATE ades_retenues SET ";
	else
	$sql = "INSERT INTO ades_retenues SET ";
$sql .= "typeDeRetenue = '$typeDeRetenue', ";
$sql .= "ladate = '".date_php_sql($ladate)."', ";
$sql .= "heure = '$heure', ";
$sql .= "duree = '$duree', ";
$sql .= "local = '$local', ";
$sql .= "places = '$places', ";
$sql .= "occupation = '$occupation', ";
$sql .= "affiche = '$affiche' ";
if ($idretenue != -1)
	$sql .= "WHERE idretenue = '$idretenue'";
// echo $sql;
$resultat = mysql_query($sql);
$idretenue = mysql_insert_id();
mysql_close($lienDB);
return $idretenue;
}

function formulaireSupprimer ()
{
$idretenue = $this->caracteristiques['idretenue'];
$typeDeRetenue = $this->caracteristiques['typeDeRetenue'];
$texte = "<h2>Suppression d'une retenue</h2>\n";
$texte .= "<form name=\"form1\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
$texte .= "<p><label>Date : </label>".$this->caracteristiques['ladate']."</p>\n";
$texte .= "<p><label>Heure : </label>".$this->caracteristiques['heure']."</p>\n";
$texte .= "<p><label>Durée : </label>".$this->caracteristiques['duree']." h</p>\n";
$texte .= "<p><label>Local : </label>".$this->caracteristiques['local']."</p>\n";
$texte .= "<p><label>Places : </label>".$this->caracteristiques['places']."</p>\n";
$texte .= "<input name=\"idretenue\" type=\"hidden\" value=\"$idretenue\">\n";
$texte .= "<input name=\"typeDeRetenue\" type=\"hidden\" value=\"$typeDeRetenue\">\n";

$texte .= "<div style=\"text-align:center\">\n";
$texte .= "<input type=\"submit\" name=\"mode\" value=\"Confirmer\">\n";
$texte .= "<input type=\"reset\" name=\"Annuler\" value=\"Annuler\" onclick=\"javascript:history.go(-1)\">\n";
$texte .= "</div>\n";
$texte .= "</form>\n";
return $texte;
}


function supprimer ($idretenue)
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "DELETE FROM ades_retenues WHERE idretenue='$idretenue'";
// echo $sql;
$resultat = mysql_query ($sql);
mysql_close ($lienDB);
return true;
}

function lireretenue($idretenue) 
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "SELECT * FROM ades_retenues WHERE idretenue='$idretenue'";
// echo $sql;
$resultat = mysql_query ($sql);
mysql_close ($lienDB);
$this->caracteristiques = mysql_fetch_assoc($resultat);
$ladate = $this->getCaracteristique('ladate');
$ladate = sh_date_sql_php($ladate);
$this->setCaracteristique('ladate', $ladate);
return true;
}

function dateDeRetenue ()
{
return date_sql_php($this->caracteristiques['ladate']);
}

}
?>