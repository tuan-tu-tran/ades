<?php
/**
 * Copyright (c) 2010 Adrien Rami
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
include ("ADESsql.class.php");
/**
 * ADESmemo:
 * 
 * classe de gestion des memos
 * 
 * @name ADESmemo
 * @author Rami Adrien ramtar@gmail.com
 * @http://www.ramtar.be
 * @copyright Rami Adrien 2010
 * @version 1.0.0
 * @package Noyau ADES
 */
 //################################################################################################
 
 class ADESmemo extends ADESsql
	{
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propriétés    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * Variables contenant les informations sur les mémos
    *
    */
	
	// Variable en rapport avec les tables mémos
	// REMARQUE: les propriétés sont communes aux mémo élèves ou user seuls la variable memoeleve détermine sur quelle table on travaille
	 
	var $id_memo_user;	 	 	 				 
	var $memo;		 		 				 
	var $ref_id_login;
	
	
    //################################################################################################
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  2. méthodes      */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  get_info_memo   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RECUPERE LES INFOS D'UN MEMO
    * ATTENTION utilisation d'un booleen pour savoir si l'on a faire avec un memo eleve ou un memo
    * 
    * @name Nom de la classe::get_info_classe
    * @param
    */
	
	public function get_info_memo($id)
	{
		//Creation de la requete de récupération des données du mémo
		$sqlgetinfomemoutilisateur = 'SELECT * FROM '.$this->prefixmysql.'ades_memo_utilisateur WHERE id_memo_user = '."'".$id."'"; 
		//Execution de la requete
		$reqmemoutilisateur = mysql_query($sqlgetinfomemoutilisateur) or die('Erreur SQL !<br>'.$sqlgetinfomemoutilisateur.'<br>'.mysql_error()); 
		$datamemoutilisateur= mysql_fetch_assoc($reqmemoutilisateur);
		
		// On récupère les données de la classe
		$this->id_memo_user = $datamemoutilisateur['id_memo_user'] ; 
		$this->memo = $datamemoutilisateur['memo'];
		$this->ref_id_login = $datamemoutilisateur['ref_id_login'];
			
			
		}
	}
	
	//################################################################################################
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  ajouter_memo     */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI AJOUTE UN MEMO
    * 
    * 
    * @name Nom de la classe:: ajouter_memo
    *
    */
	
	public function ajouter_memo()
	{
		
		$ajoutmemo = 'INSERT INTO '.$this->prefixmysql.'ades_memo_utilisateur (id_memo_user, memo, ref_id_login) VALUES ("", '."'".$this->memo."'".', '."'".$this->ref_id_login."'".' )';
		//On execute la requete mysql
		mysql_query($ajoutmemo) or die('Erreur SQL !<br>'.$ajoutmemo.'<br>'.mysql_error);
	}
	//################################################################################################
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  modif_memo  	 */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI MODIF UN MEMO
    * 
    * 
    * @name Nom de la classe:: modif_memo
    * 
    */
	
	public function modif_memo()
	{
		$modifmemo = 'UPDATE '.$this->prefixmysql.'ades_memo_utilisateur SET memo = '."'".$this->memo."'".' , ref_id_login = '."'".$this->ref_id_login."'".'WHERE id_memo_user = '.$this->id_memo_user;
		//On execute la requete mysql
		mysql_query($modifmemo) or die('Erreur SQL !<br>'.$modifmemo.'<br>'.mysql_error);
	}

	//################################################################################################
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  supprimer_memo   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI SUPPRIME UN MEMO
    * 
    * 
    * @name Nom de la classe:: supprimer_memo
    *
    */
	
	public function supprimer_memo()
	{
		//Si oui on créer une requete mysql
		$supprimermemo = 'DELETE FROM '.$this->prefixmysql.'ades_memo_utilisateur WHERE id_memo_user = '.$this->id_memo_user ;
		//On execute la requete mysql
		mysql_query($supprimermemo) or die('Erreur SQL !<br>'.$supprimermemo.'<br>'.mysql_error);
	}
 }
 ?>
