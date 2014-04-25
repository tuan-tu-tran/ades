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
/* Ades : Creation.php version 2
 * Rami Adrien: rami@adrien.be
 * Modification:
 * Supression de la suppression et de la création de la table
 * Création automatique du fichier de configuration
 * Tout le processus de configuration d'ADES est automatisé
 * Au lancement de l'application ADES détecte si ADES est configuré
 */
require("inc/init.inc.php");
Normalisation();
function CreationTables (){
	$commandes = file("./creation.sql");
	$uneCommande = "";
	$nbEtoiles = 0;
	foreach ($commandes as $uneLigne){
		// supprimer les commentaires dans le fichier .sql
		if (substr($uneCommande, 0, 2) == "--")
			$uneCommande = "";
		$uneCommande .= trim($uneLigne);
		$longueur = strlen($uneCommande);
		$dernier = substr($uneCommande, $longueur-1, 1);
		if ($dernier == ";"){
			$resultat = mysql_query($uneCommande);
			$nb = mysql_affected_rows ();
			$uneCommande = "";
		}
	}
}

function CreateConfigFile(){
	// Rami Adrien création du fichier confdb.inc.php
	$fichierconfdb = fopen(_DB_CONFIG_FILE_,"w");
	if(!$fichierconfdb){
		return false;
	}else{
		$format=<<<EOF
// SERVEUR SQL
\$sql_serveur=%s;
// LOGIN SQL
\$sql_user=%s;
// MOT DE PASSE SQL
\$sql_passwd=%s;
// NOM DE LA BASE DE DONNEES
\$sql_bdd=%s;
EOF;
		fprintf($fichierconfdb, "<?php\n".$format."\n"
			, var_export($_POST["sqlserver"], true)
			, var_export($_POST["utilisateursql"], true)
			, var_export($_POST["motdepassesql"], true)
			, var_export($_POST["nomdelabasesql"], true)
		);
		fclose($fichierconfdb);
		return true;
	}
}

function ConfigIsValid(){
	$host=$_POST["sqlserver"];
	$user=$_POST["utilisateursql"];
	$pwd=$_POST["motdepassesql"];
	$dbname=$_POST["nomdelabasesql"];
	return Db::GetInstance($host, $user, $pwd, $dbname)->connect();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Initialisation de la base de données ADES</title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
</head>
<body>
<div id="texte">
<h2>Installation d'ADES</h2>
<?php
/* Rami Adrien: Installation automatique:
 * J'ai rajouté dans le ce fichier une étape en plus à savoir la création du fichier de 
 * configuration
 *
 */
$etape = isset($_GET['etape'])?$_GET['etape']:0;
if (
	$etape!=0
	&& $etape!=1
	&& $etape!=2
	&& $etape!=3
)
	$etape=0;
// Détection pour savoir si l'on se trouve à l'étape de la création du fichier de configuration
if(empty($_POST['sqlserver'])==false){
	$etape= 2;
}
?>
<?php if($etape==0):?>

	<p>Cet utilitaire va:</p>
	<ul>
		<li>+ créer votre fichier de configuration</li>
		<li>+ créer les tables de la base de données</li>
	</ul>

	<p>Avant de commencer veuillez préparer les informations suivantes:</p>
	<ul>
		<li>+ votre serveur sql</li>
		<li>+ l'utilisateur sql</li>
		<li>+ le mot de passe</li>
		<li>+ le nom de la base de données</li>
	</ul>

	<p><big>ETES VOUS SÛR(E) DE VOULOIR POURSUIVRE?</big></p>

	<p><a href="creation.php?etape=1">Oui, je sais ce que je fais</a></p>

<?php elseif($etape==1 || $etape==2 && !ConfigIsValid()):?>

	<form name="form" method="post" action="creation.php">
		<p>
			<label>Serveur Sql :</label>
			<input value="<?php echo htmlspecialchars($_POST["sqlserver"]);?>" name="sqlserver" id="sqlserver" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Utilisateur :</label>
			<input value="<?php echo htmlspecialchars($_POST["utilisateursql"]);?>" name="utilisateursql" id="utilisateur" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Mot de Passe :</label>
			<input value="<?php echo htmlspecialchars($_POST["motdepassesql"]);?>" name="motdepassesql" id="motdepasse" size="30" maxlength="50" type="password">
		</p>
		<p>
			<label>Nom de la Base de données :</label>
			<input value="<?php echo htmlspecialchars($_POST["nomdelabasesql"]);?>" name="nomdelabasesql" id="nomdelabase" size="30" maxlength="50" type="text">
		</p>
		<input name="Submit" value="Enregistrer" type="submit">
		<?php if($etape==2):?>
		<p>Le connexion à la base de données a échoué.</p>
		<p>Le système a renvoyé l'erreur: <?php echo Db::GetInstance()->error()?></p>
		<?php endif; ?>
	</form>

<?php elseif($etape==2):?>

	<?php
		//create the config file
		if(CreateConfigFile()):
	?>
		<p>Fichier de configuration créé avec succès</p>
		<a href="creation.php?etape=3">Installation d'ADES</a>
	<?php else: ?>
		<form name="form" method="post">
		<p>Le fichier de configuration n'a pas pu être écrit.</p>
		<p>
			Veuillez vérifier que l'utilisateur système
				<b><?php echo posix_getpwuid(posix_geteuid())["name"];?></b>
			dispose des droits suffisants pour écrire le fichier
				<b><?php echo join(DIRECTORY_SEPARATOR, array(DIRNAME(__FILE__),_DB_CONFIG_FILE_));?></b>
		</p>
		<p>Le système a renvoyé l'erreur suivante: <?php echo error_get_last()["message"] ?></p>
		<input name="sqlserver" type="hidden" value="<?php echo htmlspecialchars($_POST["sqlserver"])?>" />
		<input name="utilisateursql" type="hidden" value="<?php echo htmlspecialchars($_POST["utilisateursql"])?>" />
		<input name="motdepassesql" type="hidden" value="<?php echo htmlspecialchars($_POST["motdepassesql"])?>" />
		<input name="nomdelabasesql" type="hidden" value="<?php echo htmlspecialchars($_POST["nomdelabasesql"])?>" />
		<input name="Submit" value="Réessayer" type="submit">
		</form>
	<?php endif; ?>

<?php elseif($etape==3):?>

	<?php
		//create the tables 
		if(!file_exists(_DB_CONFIG_FILE_)){
			redirect("creation.php");
		}else{
			//Si le fichier existe on l'inclut dans le programme et l'interface se charge pour l'ajout des tables
			include(_DB_CONFIG_FILE_);
			$lienDB = mysql_connect($sql_serveur, $sql_user, $sql_passwd) or die(mysql_error());
			mysql_select_db($sql_bdd);
			CreationTables();
			mysql_close ($lienDB);
		}
	?>
		<p>Login et mot de passe: admin</p>
		<p>L'installation d'ADES est terminée: <a href="index.php">On y va</a></p>
<?php endif; ?>
</div>
</body>
</html>
