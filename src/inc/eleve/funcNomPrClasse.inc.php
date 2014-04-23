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
function NomPrClasse ($ideleve,$boutonEdit)
	{
	include ("inc/classes/classeleve.inc.php");
	if (isset($ideleve))
		{
		$eleve = new eleve(-1);
		$eleve->lireEleve($ideleve);
		}
		else {jeter();}
	echo "<div style=\"width: 100%; height:auto; border:1px solid red;\">\n";
	echo "<div class=\"inv\">";
	if (boutonEdit)
	{
		//Rami Adrien: boutonEdit n'existe pas encore.
		//include ("inc/eleve/boutonEdit.inc.php");
		echo "<span id=\"onglet1\" class=\"ongletActif\" onclick=\"swap('part2', 'part1', 'onglet2', 'onglet1')\">";
		echo "Informations personnelles</span>\n";
		echo "<span id=\"onglet2\" class=\"ongletInactif\" onclick=\"swap('part1', 'part2', 'onglet1', 'onglet2')\">";
		echo "M�mo</span>\n</div>\n";
		 
		echo "<div id=\"part1\" class=\"zone\" style=\"display:block;\">\n";
		echo "<fieldset id=\"cadreGauche\">\n";
		echo "<legend>El�ve</legend>\n";
		echo "<p>Nom: <b>".$eleve->nom()."</b></p>\n";
		echo "<p>Pr�nom : <b>".$eleve->prenom()."</b></p>\n";
		echo "<p>Classe : <b>".$eleve->classe."</b></p>\n";
		echo "<p class=\"inv\">Anniversaire : <b>".$eleve->anniv()."</b></p>\n";
	}
	if ($eleve->contrat=="O"){
		echo "<span class=\"impt\">Contrat</span>\n";
	}else{
	
		echo "-\n";
		echo "</fieldset>\n";
	
		echo "<div class=\"inv\">\n";
		echo "<fieldset id=\"cadreDroit\">\n";
		echo "<legend>Parents</legend>\n";
		echo "<p>Nom du responsable : <b>".$eleve->nomresp()."</b></p>\n";
		echo "<p>Courriel des parents: <b>".$eleve->courriel()."/b></p>\n";
		echo "<p>T�l�phone : <b>".$eleve->telephone(0)."</b></p>\n";
		echo "<p>GSM : <b>".$eleve->telephone(1)."</b></p>\n";
		echo "<p>T�l�phone 2: <b>".$eleve->telephone(2)."</b></p>\n";
		echo "</fieldset>\n</div>\n";
		echo "</div>\n";
	
		echo "<div class=\"inv\">\n";
		echo "<div id=\"part2\" class=\"zone\" style=\"display:none\">\n";
		echo "<fieldset style=\"height:auto\">\n";
		echo "<legend>M�mo</legend>\n";
		echo "<b>".nl2br($eleve->memo())."</b>\n</fieldset>\n";
		echo "</div>\n";
		echo "</div>\n";
	
		echo "</div>\n";
		echo "<hr style=\"clear:both; visibility:hidden;\" />\n";
	}
	}
?>