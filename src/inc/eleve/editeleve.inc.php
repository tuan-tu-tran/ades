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
?> <table>
<tr>
<td id="onglet1" class="ongletActif inv" onclick="swap('part2', 'part1', 'onglet2', 'onglet1')">
	Informations personnelles</td>
<td id="onglet2" class="ongletInactif inv" onclick="swap('part1', 'part2', 'onglet1', 'onglet2')">
	Mémo</td>
</tr>
</table>
 
<form name="formedit2" action="##LAPAGE##" method="POST" onsubmit="return(verifForm(this))" id="formedit">
 
<div id="part1" class="zone" style="display: block;">
  
<fieldset id="cadreGauche"> 
	<legend>Elève</legend>
	<p><label for="nom">Nom :</label>
	<input name="nom" size="25" maxlength="30" value="##nom##" class="obligatoire" id="nom"> </p>
 
	<p><label for="prenom">Prénom :</label> 
	<input name="prenom" size="25" maxlength="30" value="##prenom##" class="obligatoire" id="prenom"> </p>
 
	<p><label for="classe">Classe :</label>
	<input name="classe" size="6" maxlength="6" value="##classe##" class="obligatoire" id="classe"> </p>
	
	<p><label for="anniversaire">Anniversaire :</label>
	<input name="anniv" size="5" maxlength="5" value="##anniv##" id="anniversaire"></p>
 
	<p><label for="codeinfo">Code info :</label>
	<input name="codeInfo" size="6" maxlength="6" value="##codeInfo##" id="codeinfo"></p>
  
	<p> <label for="contrat">Contrat  :</label>
	<input name="contrat" value="O" ##contrat##  type="checkbox" id="contrat"> </p>
</fieldset>

<fieldset id="cadreDroit">
	<legend>Parents</legend>
	<p><label for="nomResp">Responsable :</label>
	<input name="nomResp" size="25" maxlength="50" value="##nomResp##" id="nomResp"> </p>
 
	<p><label for="email">Courriel :</label>
	<input name="courriel" size="25" maxlength="40" id="email" value="##courriel##"></p>

	<p><label for="telephone1">Téléphone:</label> 
	<input name="telephone1" size="15" maxlength="20" value="##telephone1##" id="telephone1"></p>
 
	<p><label for="telephone2">GSM :</label> 
	<input name="telephone2" size="15" maxlength="20" value="##telephone2##" id="telephone2"></p>
 
	<p><label for="telephone3">Téléphone 2 :</label>
	<input name="telephone3" size="15" maxlength="20" value="##telephone3##"  id="telephone3"></p>
 
	<input name="ideleve" value="##ideleve##" type="hidden"> 
</fieldset>
</div>

<div id="part2" style="display: none;">
  <fieldset> 
  <legend>Mémo</legend> 
  <textarea name="memo" cols="70" rows="15">##memo##</textarea> 
  </fieldset>
</div>

<div style="text-align: center; clear:both;"> 
  <input name="mode" value="enregistrer" type="submit">
  <input name="Reset" value="Réinitialiser" type="reset">
  </div>
</form>
<script language="javascript" type="text/javascript">
document.forms[0].elements['nom'].select();
</script>