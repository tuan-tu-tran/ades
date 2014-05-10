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
require ("inc/prive.inc.php");
require ("inc/fonctions.inc.php");
require ("config/constantes.inc.php");
require ("config/confbd.inc.php");

// autorisations pour la page
autoriser("admin");  // tout le monde

$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);

// recherche de toutes les classes et tous les élèves existants dans la base de données
$sqlEleves = "DELETE FROM ades_eleves";
$sqlFaits = "DELETE FROM ades_faits";
$sqlRetenues = "DELETE FROM ades_retenues";

$Eleves = mysql_query ($sqlEleves);
$Faits = mysql_query ($sqlFaits);
$Retenues = mysql_query ($sqlRetenues);

mysql_close ($lienDB);

header('Location: index.php');   


?>


