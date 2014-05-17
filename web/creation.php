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
use EducAction\AdesBundle\Controller\Install;
use EducAction\AdesBundle\Html;

Normalisation();
$install=new EducAction\AdesBundle\Controller\Install;
$install->parseRequest();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Installation d'ADES</title>
  <link media="screen" rel="stylesheet" href="config/screen.css" type="text/css">
<style type="text/css">
	a:hover{text-decoration:underline;}
	label{width:10em;}
</style>
</head>
<body>
<div id="texte">
<h2>Installation d'ADES</h2>
<?php if($install->view==Install::VIEW_INFO):?>

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

	<p><?php $install->GetDbConfigLink("Oui, je sais ce que je fais");?></p>

<?php elseif($install->view==Install::VIEW_DB_CONFIG_FORM || $install->view==Install::VIEW_INVALID_CONFIG_SUBMITTED): ?>

		<form name="form" method="post" action="<?php echo $install->GetDbConfigSubmitUrl()?>">
		<p>
			<label>Serveur Sql :</label>
			<input value="<?echo Html::Encode($install->host)?>" name="sqlserver" id="sqlserver" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Utilisateur :</label>
			<input value="<?echo Html::Encode($install->username)?>" name="utilisateursql" id="utilisateur" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Mot de Passe :</label>
			<input value="<?echo Html::Encode($install->pwd)?>" name="motdepassesql" id="motdepasse" size="30" maxlength="50" type="password">
		</p>
		<p>
			<label>Nom de la Base de données :</label>
			<input value="<?echo Html::Encode($install->dbname)?>" name="nomdelabasesql" id="nomdelabase" size="30" maxlength="50" type="text">
		</p>

		<input name="Submit" value="Enregistrer" type="submit">

		<?php if($install->view==Install::VIEW_INVALID_CONFIG_SUBMITTED):?>
			<?php if($install->missing_fields):?>
				<p>Veuillez remplir complètement le formulaire</p>
			<?php else: ?>
		<p>Le connexion à la base de données a échoué.</p>
		<p>Le système a renvoyé l'erreur: <?php echo $install->error?></p>
			<?php endif; ?>
		<?php endif; ?>
	</form>

<?php elseif($install->view==Install::VIEW_FILE_WRITTEN):?>

		<p>Fichier de configuration créé avec succès</p>
        <?php if (!$install->tables) :?>
            <p><?php $install->GetCreateTableLink("Créer les tables de données")?></p>
        <?php else: ?>
            <p class="impt">ATTENTION! Des tables existent déjà dans la db</p>
            <ul>
                <?php foreach ($install->tables as $table) :?>
                    <li>- <?php echo $table?></li>
                <?php endforeach;?>
            </ul>
            <p><?php $install->GetCreateTableLink("Créer les tables de données quand même")?></p>
            <?php if($install->CanConfigureSchool()):?>
                <p><?php $install->GetSchoolConfigLink("Configurer le nom de l'école et le titre principal");?></p>
            <?php else:?>
                <p><a href="index.php">Terminer l'installation</a></p>
            <?php endif;?>
        <?php endif ?>

<?php elseif($install->view==Install::VIEW_FILE_NOT_WRITTEN):?>

		<form name="form" method="post" action="<?php echo $install->resubmitAction;?>">
		<p>Le fichier de configuration n'a pas pu être écrit.</p>
		<p>
			Veuillez vérifier que l'utilisateur système
				<b><?php echo $install->system_user;?></b>
			dispose des droits suffisants pour écrire le fichier
				<b><?php echo $install->config_filename;?></b>
		</p>
		<p>Le système a renvoyé l'erreur suivante: <?php echo $install->error ?></p>
		<?php foreach($_POST as $key=>$value):?>
			<input name="<?php echo $key;?>" type="hidden" value="<?php echo Html::Encode($value)?>" />
		<?php endforeach;?>
		<input name="Submit" value="Réessayer" type="submit">
		</form>

<?php elseif($install->view==Install::VIEW_TABLES_CREATED):?>

		<p>Les tables ont été correctement créées dans la base de données.</p>
		<p>Login et mot de passe: admin</p>
		<?php if($install->CanConfigureSchool()):?>
			<p><?php $install->GetSchoolConfigLink("Configurer le nom de l'école et le titre principal");?></p>
		<?php else:?>
			<p>L'installation d'ADES est terminée: <a href="index.php">On y va</a></p>
		<?php endif;?>

<?php elseif($install->view==Install::VIEW_TABLES_NOT_CREATED):?>

	<?php if(isset($install->error_command)):?>
		<p>Une erreur s'est produite lors de la creation des tables, à cause de la commande:</p>
		<p><?php echo Html::Encode($install->error_command);?></p>
	<?php else:?>
		<p>Une erreur s'est produite lors de l'exécution du script: <?php echo $install->failedScript?></p>
	<?php endif;?>
		<p>Le système a renvoyé l'erreur: <?php echo $install->error?></p>
		<p><?php $install->GetCreateTableLink("Réessayer de créer les tables");?></p>
		<p><?php $install->GetDbConfigLink("Reconfigurer la connexion (vous devez d'abord supprimer le fichier de configuration existant)");?></p>
		<?php if($install->CanConfigureSchool()):?>
			<p><?php $install->GetSchoolConfigLink("Passer à l'étape suivante: configurer le nom de l'école et le titre principal");?></p>
		<?php else:?>
			<p><a href="index.php">Terminer l'installation</a></p>
		<?php endif;?>

<?php elseif($install->view===Install::VIEW_OVERWRITE_FORBIDDEN):?>

	<p>Le fichier de configuration de la connexion à la base de données existe déjà.</p>
	<p>Pour reconfigurer la connexion, veuillez d'abord l'effacer.</p>
	<p><?php $install->GetDbConfigLink("Réessayer de configurer la connexion à la base de données");?></p>
	<p><?php $install->GetCreateTableLink("Passer à l'étape de création des tables");?></p>
	<?php if ($install->CanConfigureSchool()):?>
		<p><?php $install->GetSchoolConfigLink("Passer à l'étape de configuration du nom de l'école");?></p>
	<?php endif;?>
	<p><a href="index.php">Terminer l'installation</a></p>

<?php elseif($install->view==Install::VIEW_SCHOOL_CONFIG_FORM || $install->view==Install::VIEW_BAD_SCHOOL_CONFIG):?>

	<form action="<?php echo $install->GetSchoolConfigSubmitUrl();?>" method="POST">
		<p>
			<label>Nom de l'école :</label>
			<input value="<?echo Html::Encode($install->schoolname)?>" name="schoolname" size="30" maxlength="50" type="text">
		</p>
		<p>
			<label>Titre :</label>
			<input value="<?echo Html::Encode($install->title)?>" name="title" size="30" maxlength="50" type="text">
		</p>

		<input name="Submit" value="Enregistrer" type="submit">

		<?php if($install->view==Install::VIEW_BAD_SCHOOL_CONFIG):?>
			<p>Veuillez remplir tous les champs</p>
		<?php endif;?>
	</form>

<?php elseif($install->view==Install::VIEW_OVERWRITE_SCHOOL_FORBIDDEN):?>

	<p>Le fichier de configuration de l'école existe déjà.</p>
	<p>Pour reconfigurer l'école, veuillez utiliser <a href="confignomecole.php">l'interface d'adminstration</a>.</p>
	<p><a href="index.php">Terminer l'installation</a></p>

<?php elseif($install->view==Install::VIEW_SCHOOL_CONFIG_WRITTEN):?>

	<p>Le fichier de configuration de l'école a été écrit avec succès.</p>
	<p>L'installation d'ADES est terminée: <a href="index.php">On y va</a></p>

<?php else: ?>
	<?php throw new Exception("unhandled view: ".$install->view);?>
<?php endif;?>

</div>
</body>
</html>
