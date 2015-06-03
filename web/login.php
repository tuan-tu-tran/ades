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
require ("config/confbd.inc.php");	
require ("inc/fonctions.inc.php");
Normalisation();

// extraire l'identifiant et le mot de passe
$qui = (isset($_POST['user']))?$_POST['user']:Null;
$mdp = (isset($_POST['mdp']))?$_POST['mdp']:Null;

$ip = $_SERVER['REMOTE_ADDR'];
$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$date = date("d-m-Y");
$heure = date("H:i");
$texte = "Connexion de $qui depuis: $ip $hostname \n $date $heure";

// décommenter et ajuster la ligne suivante pour l'envoi d'un mail
// à l'administrateur lors de chaque tentative de connexion
// mail("admin@fai.net", "ADES test", $texte);

// Les données user et mdp ont été postées dans le formulaire de la page accueil.php
if (!empty($qui) && !empty($mdp)) 
	{
	// on recupère dans la BD les données utilisateur correspondant à l'identifiant
	$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd);
	mysql_select_db ($sql_bdd);
	$sql = "select * from ades_users where user='".mysql_real_escape_string($qui)."'";
	$resultat = mysql_query($sql) 
		or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	$utilisateur = @mysql_fetch_assoc($resultat);
	mysql_close($lienDB);

	if ($utilisateur['mdp'] != md5($mdp))
		// utilisateur non identifié. On recommence
		header("Location: accueil.php?erreur=faux");
		else
			{
			// utilisateur identifié. On passe à la page principale.
			session_start();
			foreach ($utilisateur as $key => $caracteristique)
				$_SESSION['identification'][$key] = $caracteristique;
			header("Location: index.php");
			$email = stripslashes($utilisateur['email']);
			$nom = stripslashes($utilisateur['user']);

			// écriture dans le fichier des logs
			$texte = "$date;$heure;$nom;$ip;$hostname";
			$handle =fopen("../local/logs.csv","a+");
			if (fwrite($handle, $texte."\n")) die();
			fclose ($handle);
			$contents = file_get_contents("../local/logs.csv");
			write ($contents);
		
			// décommenter les lignes suivantes pour provoquer l'envoi d'un mail
			// à l'utilisateur qui se connecte
			// mail({$utilisateur['email']}, 'Connexion ADES', $texte,
			//		"From: robot_ne_pas_repondre@ades_edu.net");
			} 
	}
	else
	// le nom d'utilisateur ou le mot de passe n'ont pas été donnés
	header ("Location: accueil.php?erreur=manque");
?>	
