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

/*
function formulaireChoixTypeRetenue()
{
require ("config/confbd.inc.php");
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
// $typeRetenue est éventuellement connu grâce au test sur $_REQUEST 
// sur la page retenue.php

// lire tous les types de retenues existants
$sql = "SELECT * FROM ades_typesRetenues ORDER BY typeDeRetenue ASC";
$resultat = mysql_query($sql);
// echo $sql;
mysql_close($lienDB);

// établissement de la liste de sélection à présenter dans le formulaire
// de choix du type de retenue
$options .= "<select size=\"1\" name=\"typeDeRetenue\">\n";
while ($unType = mysql_fetch_assoc($resultat))
	{
	$value = $unType['typeDeRetenue'];
	$intitule = $unType['intitule'];
	$options .= "\t<option value='$value'";
	if ($value == $typeDeRetenue) $options .= " selected";
	$options .= ">$intitule</option>\n";
	}
$options .= "</select>\n";
	
$form ="<form name=\"form1\" method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">";
$form .= $options;
$form .= "<input name=\"mode\" value=\"choixDate\" type=\"submit\">";
$form .= "</form>\n";
return $form;
}


function formulaireChoixDateRetenue ($type)
{
$listeDeRetenues = new listeDeRetenues();
// $select = listeDeRetenues->listeOptions ($typeDeRetenue);
$form ="<form name=\"form1\" method=\"POST\" action=\"{$_SERVER['PHP_SELF']}\">";
$form .= $select;
$form .= "<input name=\"mode\" value=\"listeRetenues\" type=\"submit\">";
$form .= "</form>\n";
return $form;
}
*/

?>