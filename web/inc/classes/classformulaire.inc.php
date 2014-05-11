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
class formulaire
{
var $composants = array();

function __construct ($nom="Form", $methode, $action, $onsubmit)
{
$entete = "<form name=\"$nom\" method=\"$methode\" action=\"$action\" ";
if (!empty($onsubmit)) $entete .= "onsubmit=\"$onsubmit\"";
$entete .= ">\n";
$this->composants[]=$entete;
}

function champInput ($label, $type, $nom, $valeur, $size, $maxlength, $id="", 
$class="", $onclick="", $onblur="")
{
$valeur = htmlspecialChars ($valeur);
$input = "<span class=\"label\">$label</span>\n";
$input .= "<input type=\"$type\" name=\"$nom\" value=\"$valeur\" ";
$input .= "size=\"$size\" maxlength=\"$maxlength\"";
if (!empty($id)) $input .= " id=\"$id\"";
if (!empty($class)) $input .= " class=\"$class\"";
if (!empty($onclick)) $input .= " onclick=\"$onclick\"";
if (!empty($onblur)) $input .= " onblur=\"$onblur\"";
$input .= "><br />\n";
$this->composants[]= $input;
}

function champTextarea ($label, $nom, $valeur, $lignes, $colonnes)
{
$valeur = htmlspecialChars($valeur);
$textarea = "<span class=\"label\">$label</span>\n";
$textarea .= "<textarea name=\"$nom\" rows=\"$lignes\" cols=\"$colonnes\" >";
$textarea .= "$valeur</textarea><br />\n";
$this->composants[]= $textarea;
}


function champCache ($nom, $valeur)
{
$valeur = htmlspecialChars($valeur);
$input = "<input name=\"nom\" type=\"hidden\" value=\"$valeur\">";
$this->composants[]= $input;
}
	
function cloture ()
{
$tout = $this->composants;
foreach ($tout as $champ)
	echo $champ;
echo "</form>";
}

}
?>