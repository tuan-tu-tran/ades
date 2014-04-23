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
//On inclut la classe de connexion
require("ADESsql.class.php");

// On créer un objet ADES Sql
$Search = new ADESsql;
$Search->connectDB();
// On récupère l'élément de la recherche
$q = strtolower($_GET["q"]);
//Si la variable est pas initialisé on fait un return vide
if (!$q) return;
//On récupère la valeur
$input = $_GET["q"];
//On créer un tableau de donnée
$data = array();
//On exécute la requete
$query = mysql_query("SELECT * FROM $Search->prefixmysql.ades_eleves WHERE nom LIKE '%$input%' OR prenom LIKE '%$input%'");
//On intègre les données dans un tableau
if(mysql_num_rows($query)>0)
{
	$items = array();	
	while ($row = mysql_fetch_assoc($query))
	{
		
		$stringintermediaire = $row['nom']." ".$row['prenom']." ".$row['classe'];
		$items[$stringintermediaire] = $row['ideleve'];
	}
	$Search->connectDB();
	$Search->CloseConnectDB();
	
	foreach ($items as $key=>$value) {
		if (strpos(strtolower($key), $q) !== false) {
			//echo htmlspecialchars("$key|$value\n");
			echo strip_tags("$key|$value\n");
		}
	}
	//Et on renvoi les données
	header("Content-type: application/json");
	echo json_encode(mysql_num_rows($query));
}
?>