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
?>
<form name="formDate" method="POST" action="##ADRESSE##" 
onsubmit="return(verifOrdreDates(this.date1, this.date2))">
<p>
<label for="date1">Depuis le :</label> 
<?php echo "<div id=\"cadreDroit\" class=\"micro\">$lesClasses</div>\n"; ?>
<input maxlength="10" size="12" name="date1" 
onfocus="javascript:blur();dater('formDate','date1', 'calendrier')" id="date1">
<div id="calendrier" style="position: absolute; left: 220px; z-index: 1;"></div>
</p>

<p>
<label for="date2">Jusqu'au :</label> 
<input maxlength="10" size="12" name="date2" 
onfocus="javascript:blur();dater('formDate','date2', 'calendrier');" id="date2">
</p>
<p>
<label for="classe">Classe(s) :</label>
<input maxlength="4" size="6" name="classe" id="formClasse">

</p>
<p>
<label for="typeSynthese">Type:</label>
Fiches élèves 
<input checked="checked" name="typeSynthese" value="eleves" type="radio" id="typeSyntheses">
Liste des faits
<input name="typeSynthese" value="faits" type="radio"></p>

<div style="text-align: center; margin-top: 3em;">
<input name="mode" value="Envoyer" type="submit">
<input name="Annuler" value="Annuler" type="reset">
</div>
</form>
