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

function mysql_structure($base)
	{
	$tables = mysql_list_tables($base);
	// pour toutes les tables de la base de données $base
	while ($donnees = mysql_fetch_array($tables))
		{
		// extraire le nom de la prochaine table de la BD
		$table = $donnees[0];
		$res = mysql_query("SHOW CREATE TABLE $table");
		if ($res)
			{
			$insertions = "";
			$tableau = mysql_fetch_array($res);
			// extraction du tableau de création de la table
			$tableau[1] .= ";";
			$dumpsql[] = str_replace("\n", "", $tableau[1]);
			$req_table = mysql_query("SELECT * FROM $table");
			$nbr_champs = mysql_num_fields($req_table);
			while ($ligne = mysql_fetch_array($req_table))
				{
				$insertions .= "INSERT INTO $table VALUES(";
				for ($i=0; $i<=$nbr_champs-1; $i++)
					$insertions .= "'" . mysql_real_escape_string($ligne[$i]) . "', ";
				// suppression des deux derniers caractères ", " introduits erronément
				$insertions = substr($insertions, 0, -2);
				$insertions .= ");\n";
				}
				if ($insertions != "")
					$dumpsql[] = $insertions;
			}
		}
	return implode("\r", $dumpsql);
  	}
include ("inc/fonctions.inc.php");
include ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
echo "Taille du fichier : " . file_put_contents("sqldump-".$base."-".date("Ymd-His").".sql", mysql_structure($sql_bdd));
?>