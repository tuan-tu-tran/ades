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
 * eleve de gestion des todolist
 * 
 */
 //################################################################################################
 
 class ADESeleve extends ADESsql
	{
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propriétés    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * Variables contenant les informations sur l'eleve
    *
    */
	
	//Variable en rapport avec la table eleve
	var $ideleve;
	var $nom;
	var $prenom;
	var $classe;
	var $anniv;
	var $contrat;
	var $codeInfo;
	var $nomResp;
	var $courriel;
	var $telephone1;
	var $telephone2;
	var $telephone3;
	var $memo;
	var $dermodif;
	var $idunique;
	
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  get_info_eleve   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /*
    * METHODE QUI RECUPERE LES INFOS DE L'ELEVE
    * 
    * 
    * @name Nom de la eleve::get_info_eleve
    * @param
    */
	//Creation de la requete de r�cup�ration des donn�es du mail
	function get_info_eleve()
	{
	$sqlget_info_eleve = 'SELECT * FROM '.$this->prefixmysql.'ades_eleves WHERE ideleve = '."'".$this->ideleve."'"; 
	//Execution de la requete
	$req_get_info_eleve = mysql_query($sqlget_info_eleve) or die('Erreur SQL !<br>'.$sqlget_info_eleve.'<br>'.mysql_error());
	//On remet dans les propriétés les valeurs du mail
	while($info_eleve=mysql_fetch_array($req_get_info_eleve)){
		
		$this->nom=$info_eleve['nom'];
		$this->prenom=$info_eleve['prenom'];
		$this->classe=$info_eleve['classe'];
		$this->anniv=$info_eleve['anniv'];
		$this->contrat=$info_eleve['contrat'];
		$this->codeInfo=$info_eleve['codeInfo'];
		$this->nomResp=$info_eleve['nomResp'];
		$this->courriel=$info_eleve['courriel'];
		$this->telephone1=$info_eleve['telephone1'];
		$this->telephone2=$info_eleve['telephone2'];
		$this->telephone3=$info_eleve['telephone3'];
		$this->memo=$info_eleve['memo'];
		$this->dermodif=$info_eleve['dermodif'];
		$this->idunique=$info_eleve['idunique'];
		
		}
	}

}
?>
