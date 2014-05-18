<?php
/**
 * Copyright (c) 2014 Tuan-Tu TRAN
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

use EducAction\AdesBundle\View;
use EducAction\AdesBundle\Html;
?>
<?php View::StartBlock("content")?>

	<?php if(isset($install->error_command)):?>
		<p>Une erreur s'est produite lors de la creation des tables, à cause de la commande:</p>
		<p><?php echo Html::Encode($install->error_command);?></p>
	<?php else:?>
		<p>Une erreur s'est produite lors de l'exécution du script: <?php echo $install->failedScript?></p>
	<?php endif;?>
		<p>Le système a renvoyé l'erreur: <?php echo $install->error?></p>
		<p><a href="" onclick="document.getElementById('create_table').submit(); return false;">Réessayer de créer les tables</a></p>
		<p><?php $install->GetDbConfigLink("Reconfigurer la connexion (vous devez d'abord supprimer le fichier de configuration existant)");?></p>
    <?php if($install->CanConfigureSchool()):?>
        <p><?php $install->GetSchoolConfigLink("Passer à l'étape suivante: configurer le nom de l'école et le titre principal");?></p>
    <?php else:?>
        <p><a href="index.php">Terminer l'installation</a></p>
    <?php endif;?>
    <form id="create_table" action="" method="POST" style="display:none">
    </form>

<?php View::EndBlock()?>

<?php View::Render("Install/layout.inc.php")?>





