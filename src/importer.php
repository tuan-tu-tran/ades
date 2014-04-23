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
include ("inc/prive.inc.php");
include ("inc/fonctions.inc.php");
include ("config/constantes.inc.php");
Normalisation();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title><?php echo ECOLE ?></title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
  <link media="print" rel="stylesheet" href="config/print.css" type="text/css">
  <link rel="stylesheet" href="config/menu.css" type="text/css" media="screen">
    <script language="javascript" type="text/javascript" src="inc/fonctions.js">
  </script>
</head>
<body>
<?php
// autorisations pour la page
autoriser ("admin");
// menu
require ("inc/menu.inc.php");
?>
<div id="texte">
<h2>Importation d'un fichier CSV</h2>
<?php
$mode = isset($_POST['mode']) ? $_POST['mode'] : Null;

switch ($mode)
 {
 case 'Confirmer': 
	// ouvrir la BD
	include ("config/confbd.inc.php");
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);

	$handle = fopen("./eleves.csv", "r");
	$ligne = 1;
	while (($data = fgetcsv($handle, 5000, ",","\"")) !== FALSE) 
		{
		$num = count($data);
		if ($ligne == 1)
			{
			// sur la première ligne, on trouve les intitulés des colonnes
			$debutsql = "INSERT INTO ades_eleves (";
			for ($i=0; $i < $num; $i++) 
				{
				$debutsql .= "$data[$i]";
				if ($i < $num-1) $debutsql .= ",";
				}
			$debutsql .= ") VALUES (";
			}
			else
			{
			// sur les lignes suivantes, on trouve les infos à introduire dans la BD
			$sql = $debutsql;
			for ($i=0; $i < $num; $i++) 
				{
				$sql .= "'".mysql_real_escape_string($data[$i])."'";
				if ($i < $num-1) $sql .= ","; else $sql .= ");";
				}
			mysql_query($sql);
			if (mysql_error()) 
				{ 
				echo mysql_error() ."<br>\n";  
				$erreur = true;
				}
			}
			$ligne++;
			// echo "$sql <br />";
			mysql_query($sql);
		}
		fclose($handle);
		mysql_close ($lienDB);
        if ($erreur == false)
			{
			$texte = "L'importation des données semble s'être bien passée.";
			redir ("index.php","",$texte, 5000);
            }
			else 
			{
			$texte = "Il s'est produit une erreur durant l'importation.";
			redir ("index.php","",$texte, 10000);
			}
 break;
 case 'Envoyer':
	// recopie du fichier sous un nom définitif
	$nomTemporaire = $_FILES['fichierCSV']['tmp_name'];
	if( !move_uploaded_file($nomTemporaire, "./eleves.csv") )
		exit("Impossible de copier le fichier.");
	
		echo "<div style=\"text-align: center\">\n";
        echo "<form name=\"form1\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">\n";
        echo "<p>Le fichier CSV a été transmis au serveur.</p>\n";
        echo "<p>Veuillez confirmer l'importation des données.</p>\n";
		echo "<p>\n<input type=\"reset\" name=\"submit\" value=\"Annuler\"";
		echo "onclick=\"javascript:history.go(-1)\">\n";
        echo "<input type=\"submit\" value=\"Confirmer\" name=\"mode\"></p>\n";
        echo "</form>\n";
        echo "</div>\n";

	// tableau de prévisualisation
	echo "<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\">\n";
	$handle = fopen("./eleves.csv", "r");
	while (($data = fgetcsv($handle, 5000, ",","\"")) !== FALSE) 
		{
		$num = count($data);
		echo "<tr>\n";
		for ($i=0; $i < $num; $i++) 
			echo "<td>".$data[$i] . "</td>\n";
		echo "</tr>\n";
		}
	fclose($handle);
	echo "</table>\n";
	break;
 default:
	echo "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" ";
	echo "name=\"form1\" enctype=\"multipart/form-data\">\n";
	echo "<input name=\"fichierCSV\" type=\"file\">\n";
	echo "<input name=\"mode\" value=\"Envoyer\" type=\"submit\">\n";
	echo "</form>\n";
 break;
 }
?>
</div>
<div id="pied"><?php require ("inc/notice.inc.php"); ?></div>
</body>
</html>
