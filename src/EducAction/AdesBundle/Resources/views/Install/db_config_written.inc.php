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
<?php View::EndBlock()?>

<?php View::Render("Install/layout.inc.php")?>



