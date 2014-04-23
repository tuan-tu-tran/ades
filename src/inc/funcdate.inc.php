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

function date_php_sql ($date)
{
$chiffres = explode("/", $date);
$an=$chiffres[2];
$mois=$chiffres[1];
$jour=$chiffres[0];
$date=$an."-".$mois."-".$jour;
return $date;
}

function date_sql_php ($date)
{
$joursSemaine = array ('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
// transformer la date sql en microtemps
$temps = strtotime($date);
// reconversion en date PHP
$quand = getdate($temps);
// calcul de la date
$js = $joursSemaine[$quand["wday"]];
$date = $js." ".$quand["mday"]."/".$quand["mon"]."/".$quand["year"];
return $date;
}

function sh_date_sql_php ($date)
{
// transformer la date sql en microtemps
$temps = strtotime($date);
// reconversion en date PHP
$quand = getdate($temps);
// calcul de la date
$date = $quand["mday"]."/".$quand["mon"]."/".$quand["year"];
return $date;
}

/*function touchEleve ($id)
{
$dt=date("Y-m-d");
$sql = "UPDATE ades_eleves SET dermodif = '$dt' WHERE ideleve='$id'";
$resultat = mysql_query($sql);
}*/
?>