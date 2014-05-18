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
<?php View::StartBlock("post_head")?>
    <script type="text/javascript">
        function submit()
        {
            document.getElementById("create_table").submit();
        }
    </script>
<?php View::EndBlock()?>

<?php View::StartBlock("content")?>
        <p class="impt">ATTENTION! Des tables existent déjà dans la base de données:</p>
        <ul>
            <?php foreach ($install->tables as $table) :?>
                <li>- <?php echo $table?></li>
            <?php endforeach;?>
        </ul>
        <p><a href="" onclick="submit(); return false;">Créer les tables de données quand même</a></p>
        <?php if($install->CanConfigureSchool()):?>
            <p><?php $install->GetSchoolConfigLink("Configurer le nom de l'école et le titre principal");?></p>
        <?php else:?>
            <p><a href="index.php">Terminer l'installation</a></p>
        <?php endif;?>
        <form id="create_table" action="" method="POST" style="display:none">
        </form>
<?php View::EndBlock()?>

<?php View::Render("Install/layout.inc.php")?>

