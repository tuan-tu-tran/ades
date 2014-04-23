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
?><form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" 
onsubmit="return(verif(this))">

<p><label for="user">Utilisateur :</label>
<input name="user" type="text" id="user" value="<?php echo $user; ?>" class="obligatoire"></p>

<p><label for="nom">Nom :</label>
<input name="nom" type="text" id="nom" value="<?php echo $nom; ?>" class="obligatoire"></p>

<p><label for="prenom">Pr&eacute;nom :</label>
<input name="prenom" type="text" id="prenom" value="<?php echo $prenom; ?>" class="obligatoire"></p>

<p><label for="email">Courriel :</label>
<input name="email" type="text" id="email" value="<?php echo $email; ?>" class="obligatoire"></p>

<p><label for="mdp1">Mot de passe :</label>
<input name="mdp1" type="password" id="mdp1" value="<?php echo $mdp;?>" class="obligatoire"></p>

<p><label for="mdp2">Confirmation :</label>
<input name="mdp2" type="password" id="mdp2" value="<?php echo $mdp;?>" class="obligatoire"></p>

<p><label for="privilege">Privil&egrave;ge :</label>
<select name="privilege" id="privilege">
<?php 
$privileges = array ("readonly"=>"Lecture seule", "educ"=>"Educateur", "admin"=>"Administrateur");
$texte = '';
foreach ($privileges as $unPrivilege=>$unTitre)
	{
	$texte .= "\t<option value=\"$unPrivilege\"";
	if ($unPrivilege == $privilege)
		$texte .= " selected";
	$texte .= ">$unTitre</option>\n";
	}
echo $texte;
?>
</select></p>
<p><label for="timeover">Dur&eacute;e de session :</label>
<input name="timeover" type="text" id="timeover" size="4" maxlength="2" 
	value="<?php echo $timeover;?>" class="obligatoire"> minutes
<input name="idedu" type="hidden" value="<?php echo $idedu;?>">
<div style="text-align:center">
<input type="submit" name="mode" value="Enregistrer">
<input name="R&eacute;initialiser" value="Annuler" type="reset">
</div>
</form>