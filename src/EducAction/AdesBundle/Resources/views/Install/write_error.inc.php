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
<?php View::EndBlock()?>

<?php View::Render("Install/layout.inc.php")?>


