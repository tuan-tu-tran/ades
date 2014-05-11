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
include_once ("ADESsql.class.php");
 /*
 * ADEStodolist:
 * 
 * gestion des user
 * 
 */
 //################################################################################################
 
 class ADESuser extends ADESsql
	{
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propriétés    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * Variables contenant les informations sur l'utilisateur
    *
    */
	
	//Variable en rapport avec la table users
	var $idedu;
	var $user;
	var $nom;
	var $prenom;
	var $email;
	var $mdp;
	var $privilege;
	var $timeover;
	
	
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  get_info_user    */
    /*~*~*~*~*~*~*~*~*~*~*/
    /*
    * METHODE QUI RECUPERE LES INFOS D'UN USER
    * 
    * 
    * @name Nom de la eleve::get_info_user
    * @param
    */
	//Creation de la requete de r�cup�ration des donn�es du mail
	function get_info_user()
	{
	$sqlget_info_user = 'SELECT * FROM '.$this->prefixmysql.'ades_users WHERE idedu = '."'".$this->idedu."'"; 
	//Execution de la requete
	$req_get_info_user = mysql_query($sqlget_info_user) or die('Erreur SQL !<br>'.$sqlget_info_user.'<br>'.mysql_error());
	//On remet dans les propriétés les valeurs du mail
	while($info_user=mysql_fetch_array($req_get_info_user)){
		
		$this->user=$info_user['user'];
		$this->nom=$info_user['nom'];
		$this->prenom=$info_user['prenom'];
		$this->email=$info_user['email'];
		$this->privilege=$info_user['privilege'];
		$this->timeover=$info_user['timeover'];
		
		}
	}
	function get_list_user($typeuser)
	{
		
		$sqlget_list_user = 'SELECT idedu FROM '.$this->prefixmysql.'ades_users WHERE privilege LIKE \''.$typeuser.'\''; 
		//Execution de la requete
		$req_get_list_user = mysql_query($sqlget_list_user) or die('Erreur SQL !<br>'.$sqlget_list_user.'<br>'.mysql_error());
		//On remet dans les propriétés les valeurs du mail
		$result_user_html = "<SELECT name=\"ChoixUser_".$typeuser."\" id=\"ChoixUser_".$typeuser."\">";
		while($data_user = mysql_fetch_assoc($req_get_list_user))
			 {
			 	$this->idedu = $data_user['idedu'];
			 	$this->get_info_user();
			 	$result_user_html .= "<option value=\"".$data_user['idedu']."\">".$this->nom." ".$this->prenom."</option>\n";
				
			 }
		$result_user_html .= "</select>";
		return $result_user_html;
	}

}
?>
