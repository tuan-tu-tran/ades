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
require "inc/init.inc.php";
EducAction\AdesBundle\User::CheckIfLogged();
require ("fpdf/fpdf.php");
require ("inc/funcdate.inc.php");
Normalisation();

$idfait = isset($_GET['idfait'])?$_GET['idfait']:Null;
if (!(isset($idfait))) jeter();

// Rechercher les références del'élève connaissant le "idfait"
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);

$sql = "SELECT ades_faits.* , ades_retenues.typeDeRetenue, ";
$sql .= "ades_retenues.ladate AS dateRetenue, ades_retenues.heure, ";
$sql .= "ades_retenues.local, ades_retenues.duree, nom, prenom, classe ";
$sql .= "FROM ades_faits LEFT JOIN ades_retenues ";
$sql .= "ON ades_faits.idretenue = ades_retenues.idretenue ";
$sql .= "LEFT JOIN ades_eleves ON ades_faits.ideleve = ades_eleves.ideleve ";
$sql .= "WHERE idfait = $idfait";

// echo $sql;

$resultat = mysql_query ($sql);
$infos = mysql_fetch_assoc($resultat);

$intituleDesRetenues = parse_ini_file("config/intitulesretenues.ini", TRUE);

// le numéro de type de retenue
$typeDeRetenue = $infos['typeDeRetenue'];

// permet de retrouver l'intitulé du type, issu du fichier .ini
$intitule = $intituleDesRetenues[$typeDeRetenue]['intitule'];

// lecture de toutes les variables retournées par la requête $sql
foreach ($infos as $key=>$value)
	$$key = stripslashes($value);
$dateRetenue = date_sql_php($infos['dateRetenue']);

require ("config/billetretenue.inc.php");

?>
