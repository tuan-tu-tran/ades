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
require ("inc/classes/classlisteretenues.inc.php");
$listeTypesRetenues = new listesDeRetenues();

// le type de retenue ne peut être modifié que si aucun élève n'y est inscrit
if ($occupation == 0)
	// lire la liste de tous les types de retenues possibles
	$options = $listeTypesRetenues->selectTypeRetenue($typeDeRetenue);
	else
	{
	// lire l'intitulé de la retenue de type $typeDeRetenue
	$options .= $listeTypesRetenues->intitule($typeDeRetenue);
	$options .= "<input name=\"typeDeRetenue\" id=\"typeDeRetenue\" value=\"$typeDeRetenue\" type=\"hidden\">\n";
	$options .= "<br />\n";
	}
?>
	
<h2>Edition d'une retenue</h2>
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>"
 	onsubmit="return(verifNouvelleRetenue(this))">
	<p><label for="typeDeRetenue">Type de retenue:</label>
	<?php echo $options; ?>
	</p>
	<p><label for="ladate">Date :</label>
	<input name="ladate" class="obligatoire" id="ladate" value="<?php echo date("d/m/y");?>" 
	size="10" maxlength="10" onfocus="javascript:blur();dater(0,1,'calendrier');" type="text">
	<span id="calendrier" style="position: absolute; z-index: 100;"></span></p>
	<p><label for="heure">Heure :</label>
	<input maxlength="5" size="5" name="heure" id="heure" value="<?php echo $heure;?>"
	class="obligatoire" id="heure" onfocus="javascript:blur();" type="text"> => 
	<?php
	include ("heure/horloge.inc.php");
	?>
	</p>
	<p><label for="duree">Durée :</label>
	<select name="duree" id="duree">
		<option value="1" <?php if ($duree==1) echo "selected" ?>>
		1 heure</option>
		<option value="2" <?php if ($duree==2) echo "selected" ?>>
		2 heures</option>
		<option value="3" <?php if ($duree==3) echo "selected" ?>>
		3 heures</option>
	</select>
	</p>
	<p><label for="local">Local :</label>
<?php if(!isset($local)){$local="";}?>
	<input name="local" id="local" value="<?php echo $local;?>" size="30" maxlength="30" class="obligatoire" type="text"></p>
	<p><label for="places">Places :</label>
<?php if(!isset($places)){$places="";}?>
	<input name="places" id="places" value="<?php echo $places;?>" size="4" maxlength="2" class="obligatoire" type="text">	
	<?php echo " minimum $occupation place(s)" ?></p>
	<input name="occupation" value="<?php echo $occupation;?>" type="hidden">
	<p><label>Occupation :</label><?php echo $occupation;?></p>
	<p><label>Affiché :</label>
	Oui <input <?php if ($affiche=='O') echo "checked" ?> name="affiche" value="O" type="radio">
	Non <input <?php if ($affiche!='O') echo "checked" ?> name="affiche" value="N" type="radio">
	<input name="idretenue" value="<?php echo $idretenue;?>" type="hidden">

<div style="text-align:center"> 
<input name="mode" value="Enregistrer" type="submit"> 
<input name="Submit" value="Réinitialiser" type="reset">
</div>
</form>
