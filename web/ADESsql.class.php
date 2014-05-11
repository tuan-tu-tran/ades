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

/**
 * ADESsession:
 * 
 * classe de session et d'identification d'ADES
 * 
 */
 //################################################################################################
 class ADESsql {
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propri�t�s    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * @var prefixmysql, qui r�cup�re le pr�fixe des tables
    *
    */
	var $prefixmysql;
	var $iduseractuel;
	var $lienDB;
	public $connection;
	
    //################################################################################################
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  2. m�thodes      */
    /*~*~*~*~*~*~*~*~*~*~*/
	/**

	/*~*~*~*~*~*~*~*~*~*~*/
    /*  Constructeur     */
    /*~*~*~*~*~*~*~*~*~*~*/
	/**
    * Constructeur
    * Connexion � la base de donn�es SQL pour pouvoir attaquer la base de donn�es SQL
    * 
    */
	    public function connectDB() 
	{
		require("config/confbd.inc.php");
		$this->lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
		mysql_select_db ($sql_bdd);
		$this->prefixmysql=$sql_prefix;
    }
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  getiduser	     */
    /*~*~*~*~*~*~*~*~*~*~*/
	/**
    * getiduser
    * Fonction qui permet d'obtenir l'id du user
    * 
    */
		public function getiduser($nomuser)
	{
		
		$sql = "select idedu from ades_users where user=\"".$nomuser."\"";
		$resultat = mysql_query($sql) 
		or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$utilisateur = @mysql_fetch_assoc($resultat);
		$this->iduseractuel = $utilisateur['idedu'];
	}
	public function	CloseConnectDB()
	{
		mysql_close($this->lienDB);
	}
	//Connection avec PDO
	public function __connection()
{
	require("config/confbd.inc.php");
	$PARAM_hote=$sql_serveur; // le chemin vers le serveur
	$PARAM_port='';
	$PARAM_nom_bd=$sql_bdd; // le nom de votre base de données
	$PARAM_utilisateur=$sql_user; // nom d'utilisateur pour se connecter
	$PARAM_mot_passe=$sql_passwd; // mot de passe de l'utilisateur pour se connecter
	$this->prefixmysql=$sql_prefix;
	$this->connection= new PDO('mysql:host='.$PARAM_hote.';port='.$PARAM_port.';dbname='.$PARAM_nom_bd, $PARAM_utilisateur, $PARAM_mot_passe);
}
 }
 ?>
