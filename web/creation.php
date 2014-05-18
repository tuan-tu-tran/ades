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
<?php elseif($install->view==Install::VIEW_DB_CONFIG_FORM || $install->view==Install::VIEW_INVALID_CONFIG_SUBMITTED): ?>

<?php elseif($install->view==Install::VIEW_FILE_WRITTEN):?>


<?php elseif($install->view==Install::VIEW_FILE_NOT_WRITTEN):?>
<?php elseif($install->view==Install::VIEW_TABLES_CREATED):?>


<?php elseif($install->view==Install::VIEW_TABLES_NOT_CREATED):?>


<?php elseif($install->view===Install::VIEW_OVERWRITE_FORBIDDEN):?>


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
