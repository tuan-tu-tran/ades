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
 
 class ADESminimail extends ADESsql
	{
 
    /*~*~*~*~*~*~*~*~*~*~*/
    /*  1. propri�t�s    */
    /*~*~*~*~*~*~*~*~*~*~*/
    
    /**
    * Variables contenant les informations sur la classe
    *
    */
   //Variable en rapport avec la table ades_boite_email
   
	var $id_boite_email;
	var $ref_user;
	var $ref_mail_boite_email;
	var $ref_dossier;
	var $Lu;
	
	//Variable en rapport avec la table ades_mail
	
	var $id_mail;
	var $ref_expediteur;
	var $ref_mail;
	var $sujet;
	var $texte;
	var $brouillon;
	var $date_envoi;
	
	//Variable en rapport avec la table ades_mail_destinataire
	
	var $id_mail_destinataire;
	var $ref_mail_destinataire;
	var $ref_id_user;
	
	//Variable en rapport avec la table ades_users
	
	var $id;
	var $nom;
	var $prenom;
	var $email;
	
	//Variable de la classe minimail
	var $resulthtml;
	
	//Variable pour récupérer les destinataires
	var $IdDestinataire;
	var $Destinataire;
	
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /* get_nbrlu_minimail*/
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RECUPERE LA LISTE DES MINIMAILS
    * 
    * 
    * @name Nom de la classe::get_minimail
    * @param
    */
   public function get_nbrlu_minimail()
	{
		$this->getiduser($_SESSION['identification']['user']);	
		//Creation de la requete de r�cup�ration du nombre de minimail
		$sqlget_nbrlu_minimail = 'SELECT COUNT(*) FROM '.$this->prefixmysql.'ades_boite_email WHERE Lu = 0 AND ref_user = '.$this->iduseractuel;
		//Execution de la requete
		$req_nbrlu_minimail = mysql_query($sqlget_nbrlu_minimail) or die('Erreur SQL !<br>'.$sqlget_nbrlu_minimail.'<br>'.mysql_error());
		$resultmail=mysql_fetch_array($req_nbrlu_minimail);
		return $resultmail[0];
	}	
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  del_minimail     */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI SUPPRIME UN minimail
    * 
    * 
    * @name Nom de la classe::del_minimail
    * @param
    */
	
	public function del_minimail($minimailasupprimer)
	{
		//On créer la requête pour supprimer l'email
		$reqdelminimail = 'DELETE FROM '.$this->prefixmysql.'ades_boite_email WHERE ref_mail = '.$minimailasupprimer." AND ref_user = ".$this->iduseractuel;
		//On l'exécute
		mysql_query($reqdelminimail) or die('Erreur SQL !<br>'.$reqdelminimail.'<br>'.mysql_error());
		
		//On test si le mail n'est plus présent dans aucune boite de réception
		//Création de la requete pour détecter si le mail est encore présent dans une boite email
		$sqlgetinfoBmail = 'SELECT * FROM '.$this->prefixmysql.'ades_boite_email WHERE ref_mail = '."'".$minimailasupprimer."'";
		//On l'exécute
		$reqBmail = mysql_query($sqlgetinfoBmail) or die('Erreur SQL !<br>'.$sqlgetinfoBmail.'<br>'.mysql_error());
		
		//On test si la requête renvoi un résultat, si ce n'est pas le cas on efface le mail définitivement
		if(mysql_num_rows($reqBmail)<=0)
		{
			//Création de la requête de suppression de mail
			$reqdelmail = 'DELETE FROM '.$this->prefixmysql.'ades_mail WHERE id_mail = '.$minimailasupprimer;
			//On l'exécute
			mysql_query($reqdelmail) or die('Erreur SQL !<br>'.$reqdelmail.'<br>'.mysql_error());
			//Création de la requête de suppresion des destinataires du mail => le mail n'existe plus
			$reqdelmailDestinataire = 'DELETE FROM '.$this->prefixmysql.'ades_mail_destinataire WHERE ref_mail = '.$minimailasupprimer;
			mysql_query($reqdelmailDestinataire) or die('Erreur SQL !<br>'.$reqdelmailDestinataire.'<br>'.mysql_error());
		}
	}
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /* get_liste_minimail*/
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RECUPERE LA LISTE DES MINIMAILS
    * 
    * 
    * @name Nom de la classe::get_minimail
    * @param
    */
   public function get_liste_minimail()
	{
		//On récupere le user
		$this->getiduser($_SESSION['identification']['nom']);
		//Creation de la requete de r�cup�ration des donn�es de todo
		$sqlgetget_liste_minimail = 'SELECT ref_mail, Lu FROM '.$this->prefixmysql.'ades_boite_email WHERE ref_user = '."'".$this->iduseractuel."'"." and ref_dossier = '".$this->ref_dossier."' ORDER BY ref_mail DESC "; 
		//Execution de la requete
		$reqliste_minimail = mysql_query($sqlgetget_liste_minimail) or die('Erreur SQL !<br>'.$sqlgetget_liste_minimail.'<br>'.mysql_error());
		//On teste si la requête renvoi un résultat
		$nombremail = mysql_num_rows($reqliste_minimail);
		if($nombremail>0)
		{
			//Si oui on créer la liste des emails
			$this->resulthtml.="<TABLE id=\"TableauMail\" BORDER=\"1\" WIDTH=\"380\" >";
			while($dataliste_minimail = mysql_fetch_array($reqliste_minimail)){
				$this->id_mail=$dataliste_minimail['ref_mail'];
				$this->get_minimail();
				
				//On créé un tableau en html avec la liste des emails et un lien vers le mail pour le mail
				$this->resulthtml.="<TR>\n";
				$this->resulthtml.="<TH>";
				if(!($dataliste_minimail['Lu'])) {
					$this->resulthtml.="<i>";
					$this->resulthtml.="<u>";
					}
				$idmailboucle = $dataliste_minimail['ref_mail'];
				$this->resulthtml.="<a href=\"readmail.php?idmail=$idmailboucle\">";
				$this->resulthtml.= $this->nom." ".$this->prenom;
				$this->resulthtml.="	l	";	
				$this->resulthtml.= $this->sujet;
				$this->resulthtml.="	l	";
				$this->resulthtml.= "<i>".substr($this->texte, 0, 40)."</i>...";
				$this->resulthtml.="	l	";	
				$this->resulthtml.= $this->date_envoi;
				$this->resulthtml.="</a>";
				if(!($dataliste_minimail['Lu'])) {
					$this->resulthtml.="</u>";
					$this->resulthtml.="</i>";
					}
				$this->resulthtml.="</TH>\n";
				
				}
				$this->resulthtml.="</TABLE>";			
		}else{
			$this->resulthtml="Aucun email";
		}
	}
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  lu_minimail     */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI MES UN MAIL LU
    * 
    * 
    * @name Nom de la classe::lu_minimail
    * @param
    */
   public function lu_minimail()
   {
	     //Creation de la requete de r�cup�ration des donn�es du mail
		$sqlLu_minimail = 'UPDATE '.$this->prefixmysql.'ades_boite_email SET Lu = 1 WHERE ref_mail = '."'".$this->id_mail."'"; 
		//Execution de la requete
		$reqLu_minimail = mysql_query($sqlLu_minimail) or die('Erreur SQL !<br>'.$sqlLu_minimail.'<br>'.mysql_error());
		//On remet dans les propriétés les valeurs du mail
		
		
   }
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  get_minimail     */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI RECUPERE UN MINI MAIl
    * 
    * 
    * @name Nom de la classe::get_minimail
    * @param
    */
   public function get_minimail()
   {
	    //Creation de la requete de r�cup�ration des donn�es du mail
		$sqlgetget_minimail = 'SELECT * FROM '.$this->prefixmysql.'ades_mail WHERE id_mail = '."'".$this->id_mail."'"; 
		//Execution de la requete
		$reqliste_minimail = mysql_query($sqlgetget_minimail) or die('Erreur SQL !<br>'.$sqlgetget_minimail.'<br>'.mysql_error());
		//On remet dans les propriétés les valeurs du mail
		while($infomail=mysql_fetch_array($reqliste_minimail)){
				
			$this->id_mail=$infomail['id_mail'];
			$this->ref_expediteur=$infomail['ref_expediteur'];
			//Creation de la requete de r�cup�ration des donn�es de mail
			$sqlget_minimail_nom_expediteur = 'SELECT nom, prenom FROM '.$this->prefixmysql.'ades_users WHERE idedu = '."'".$this->ref_expediteur."'";
			//Execution de la requete
			$reqminimail_nom_expediteur = mysql_query($sqlget_minimail_nom_expediteur) or die('Erreur SQL !<br>'.$sqlget_minimail_nom_expediteur.'<br>'.mysql_error());
			//On créer le tableau
			$result_reqminimail_nom_expediteur = mysql_fetch_array($reqminimail_nom_expediteur);
			$this->nom = $result_reqminimail_nom_expediteur['nom'];
			$this->prenom = $result_reqminimail_nom_expediteur['prenom'];
			$this->ref_mail=$infomail['ref_mail'];
			$this->sujet=$infomail['sujet'];
			$this->texte=$infomail['texte'];
			$this->brouillon=$infomail['Brouillon'];
			$this->date_envoi=$infomail['date_envoi'];
		}
		//Quand on a récupéré les données du mail on récupère les id des destinataire pour la réponse
		$sqlget_minimail_destinataire = 'SELECT * FROM '.$this->prefixmysql.'ades_mail_destinataire WHERE ref_mail = '."'".$this->id_mail."'";
		//Execution de la requete
		$reqminimail_destinataire = mysql_query($sqlget_minimail_destinataire) or die('Erreur SQL !<br>'.$sqlget_minimail_destinataire.'<br>'.mysql_error());
		//On ajoute l'expéditeur du mail
		$this->IdDestinataire[0]= $this->ref_expediteur;
		$this->Destinataire[0]= $_SESSION['identification']['nom']." ".$_SESSION['identification']['prenom'];
		//On initialise une variable d'incrémentation pour les tableaux
		$i = 1;
		while($infomaildestinataire=mysql_fetch_array($reqminimail_destinataire)){
			if($infomaildestinataire['ref_id_user']!=$this->ref_expediteur and $infomaildestinataire['ref_id_user']!= $this->iduseractuel)
			{
				$this->IdDestinataire[$i] = $infomaildestinataire['ref_id_user'];
				//Quand on a récupéré les données du mail on récupère les id des destinataire pour la réponse
				$sqlget_minimail_nom_destinataire = 'SELECT nom, prenom FROM '.$this->prefixmysql.'ades_users WHERE idedu = '."'".$infomaildestinataire['ref_id_user']."'";
				//Execution de la requete
				$reqminimail_nom_destinataire = mysql_query($sqlget_minimail_nom_destinataire) or die('Erreur SQL !<br>'.$sqlget_minimail_nom_destinataire.'<br>'.mysql_error());
				$infomailNomDestinataire =	mysql_fetch_array($reqminimail_nom_destinataire);
				$this->Destinataire[$i] = $infomailNomDestinataire['nom']." ".$infomailNomDestinataire['prenom'];
				$i++;
			}
		}
   }
	//################################################################################################
	/*~*~*~*~*~*~*~*~*~*~*/
    /*  envoi_minimail   */
    /*~*~*~*~*~*~*~*~*~*~*/
    /**
    * METHODE QUI ENVOI UN MINI MAIl
    * 
    * 
    * @name Nom de la classe::envoi_minimail
    * @param
    */
   public function envoi_minimail($destinataire)
   {	
   		//Récupération de l'id de l'expéditeur
   		$this->getiduser($_SESSION['identification']['nom']);
   		$this->ref_expediteur =  $this->iduseractuel;
	     //Creation de la requete de l'envoi du mail'
		$sqlEnvoi_minimail = 'INSERT INTO '.$this->prefixmysql.'ades_mail (ref_expediteur, ref_mail, sujet, texte, Brouillon, date_envoi) VALUE ('.$this->ref_expediteur.','.$this->ref_mail.',"'.$this->sujet.'","'.$this->texte.'", 0 ,"'.date("Y-m-d H:i:s").'")'; 
		//Execution de la requete
		$reqEnvoi_minimail = mysql_query($sqlEnvoi_minimail) or die('Erreur SQL !<br>'.$sqlEnvoi_minimail.'<br>'.mysql_error());
		$this->id_mail = mysql_insert_id();
		
		//Boucle For Each pour enregistré tout les destinataires du mail
		foreach($destinataire as $iddesti){
			//Creation de la requete de l'enregistrement des destinataires'
			$sqlEnvoi_minimail_Destinataire = 'INSERT INTO '.$this->prefixmysql.'ades_mail_destinataire (ref_mail, ref_id_user) VALUE ('.$this->id_mail.','.$iddesti.')'; 
			//Execution de la requete
			$reqEnvoi_minimail_Destinataire = mysql_query($sqlEnvoi_minimail_Destinataire) or die('Erreur SQL !<br>'.$sqlEnvoi_minimail_Destinataire.'<br>'.mysql_error());
			
			//Creation de la requete pour mettre le message dans toutes les boites emails
			$sqlEnvoi_minimail_Boite_email = 'INSERT INTO '.$this->prefixmysql.'ades_boite_email (ref_user, ref_mail, ref_dossier, Lu) VALUE ('.$iddesti.','.$this->id_mail.', 1 , 0)'; 
			//Execution de la requete
			$reqEnvoi_minimail_Boite_email = mysql_query($sqlEnvoi_minimail_Boite_email) or die('Erreur SQL !<br>'.$sqlEnvoi_minimail_Boite_email.'<br>'.mysql_error());
		
		
	}
   }
  }
?>
