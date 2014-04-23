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
 * classe de gestion des todolist
 * 
 */
 //################################################################################################
 
 class ADEStodo extends ADESsql
	{
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propri�t�s    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * Variables contenant les informations sur la classe
    *
    */
	
	//Variable en rapport avec la table todo
	var $id_todo;
	var $ref_personne;
	var $todo;
	var $resulthtml;
	
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  get_info_todo   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RECUPERE LES INFOS D'UNE LISTE TODO
    * 
    * 
    * @name Nom de la classe::get_info_todo
    * @param
    */
	
	private function get_info_todo()
	{
		//Creation de la requete de r�cup�ration des donn�es de todo
		$sqlgetinfotodo = 'SELECT * FROM '.$this->prefixmysql.'ades_todo WHERE ref_personne = '."'".$this->iduseractuel."'"; 
		//Execution de la requete
		$reqtodo = mysql_query($sqlgetinfotodo) or die('Erreur SQL !<br>'.$sqlgetinfotodo.'<br>'.mysql_error());
		if(mysql_num_rows($reqtodo)>0)
		{
			//$datatodo = mysql_fetch_assoc($reqtodo);
			$this->resulthtml.="<form name=\"formsupressionmemo\" id=\"formsupressionmemo\">";
			$this->resulthtml.="<TABLE BORDER=\"1\" WIDTH=\"380\">";
			while($datatodo = mysql_fetch_array($reqtodo)){
				$this->resulthtml.="<TR>\n";
				$this->resulthtml.="<TH>";
				$this->resulthtml.="<INPUT type=\"checkbox\" name=\"todolist[]\" id=\"todolist".$datatodo['id_todo']."\" value=\"".$datatodo['id_todo']."\">";
				$this->resulthtml.="</TH>\n";
				$this->resulthtml.="<TH>";
				$this->resulthtml.=$datatodo['todo'];
				$this->resulthtml.="</TH>\n";
				$this->resulthtml.="</form>";
			}
			$this->resulthtml.="</TABLE>";
			$this->resulthtml.="<input value=\"Supprimer\" type=\"button\" onClick='SupprimerMemo();'></br></br>";
			
		}else{
			$this->resulthtml="Aucun m&eacute;mo";
		}
		
		// On r�cup�re les diff�rentes donn�es du todo
		//$this->id_todo = $dataclasse['id_todo'];
		//$this->ref_personne = $dataclasse['ref_personne'];
		//$this->todo = $dataclasse['todo'];
	
	}
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  send_todo_info   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RETURN LE RESULTAT DE LA FONCTION get_info_todo
    * 
    * 
    * @name Nom de la classe::send_todo_info
    * @param
    */
	//################################################################################################
	public function send_todo_info()
	{	
		$this->getiduser($_SESSION['identification']['nom']);
		$this->get_info_todo($this->iduseractuel);
		return $this->resulthtml;
	}
	
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  ajout_todo       */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI AJOUTE UN TODO
    * 
    * 
    * @name Nom de la classe::ajout_todo
    * @param
    */
	
	public function ajout_todo()
	{
		$this->getiduser($_SESSION['identification']['nom']);
		$reqajoutnewtodo = 'INSERT INTO '.$this->prefixmysql.'ades_todo (ref_personne, todo) VALUES ('."'".$this->iduseractuel."'".', '."'".$this->todo."'".')';
		mysql_query($reqajoutnewtodo) or die('Erreur SQL !<br>'.$reqajoutnewtodo.'<br>'.mysql_error()); 
	}
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  modif_todo       */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI MODIF UN TODO
    * 
    * 
    * @name Nom de la classe::modif_todo
    * @param
    */
	
	public function modif_todo()
	{	
		$this->getiduser($_SESSION['identification']['nom']);
		$reqmodiftodo = 'UPDATE '.$this->prefixmysql.'ades_todo SET ref_personne = '."'".$this->iduseractuel."'".' , todo = '."'".$this->todo."'".' WHERE id_todo =' .$this->id_todo;
		mysql_query($reqmodiftodo) or die('Erreur SQL !<br>'.$reqmodiftodo.'<br>'.mysql_error()); 
	}
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  del_todo         */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI SUPPRIME UN TODO
    * 
    * 
    * @name Nom de la classe::modif_todo
    * @param
    */
	
	public function del_todo($todoasupprimer)
	{
		foreach($todoasupprimer as $elementasupprimer)
		{
		$reqdeltodo = 'DELETE FROM '.$this->prefixmysql.'ades_todo WHERE id_todo = '.$elementasupprimer;
		mysql_query($reqdeltodo) or die('Erreur SQL !<br>'.$reqdeltodo.'<br>'.mysql_error());
		}
	}
	} 
?>
