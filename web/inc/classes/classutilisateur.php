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
class utilisateur {
var $pseudo, $nom, $prenom, $courriel, $privilege;
// constructeur vide...
function __construct ()
{}

function verifieMdp ($user,$mdp)
{
$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
mysql_select_db ($sql_bdd);
$sql = "select user, mdp, nom, prenom, email, privilege from ades_users where user='$user'";
$resultat = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
$utilisateur = @mysql_fetch_array($resultat);
mysql_close($lienDB);

if (!($utilisateur['mdp'] == $mdp))
	{
	// utilisateur non identifié
	return false;
	}
	else
	{
	// utilisateur identifié. on charge ses caractéristiques
	$this->pseudo = $utilisateur['user'];
	$this->nom = $utilisateur['nom'];
	$this->prenom = $utilisateur['prenom'];
	$this->courriel = $utilisateur['email'];
	$this->privilege = $utilisateur['privilege'];
	return true;
	} 
}
}

/*---------------------------------------*/
?>