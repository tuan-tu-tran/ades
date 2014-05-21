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
    <style type="text/css">
        form#config_detention_slip label
        {
            float:none;
            display:inline-block;
            width:10em;
            cursor:pointer;
        }
    </style>
<?php View::EndBlock()?>

<?php View::StartBlock("content")?>
    <h2>Configuration billet retenue</h2>

    <?php if ($errors) :?>
        <fieldset class="notice impt">
            <legend>Erreurs</legend>
            <?php foreach($errors as $e) :?>
                <p><?php echo Html::Encode($e)?></p>
            <?php endforeach ?>
        </fieldset>
    <?php endif ?>

    <p style="margin-left: 1em"><a href="apercubilletretenue.php" target="_blank">Visualiser un aperçu du billet de retenue</a></p>
    <form id="config_detention_slip" method="POST" enctype="multipart/form-data">
            <label>Type Impression :</label>
            <select name="typeimpression" id="typeimpression">
                <?php Html::Option("Paysage", NULL, $paysage) ?>
                <?php Html::Option("Portrait", NULL, !$paysage) ?>
            </select>
            <p>
                <label for="fichierimagebilletretenue">Image de l'école :</label>
                <input type="file" id="fichierimagebilletretenue" name="fichierimagebilletretenue"/>
            <p>
                <label for="nomecole">Nom de l'école :</label>
                <input name="nomecole" id="nomecole" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($nomecole)?>" />
            <p>
                <label for="adresseecole">Adresse de l'école :</label>
                <input name="adresseecole" id="adresseecole" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($adresseecole)?>" />
            <p>
                <label for="lieu">Lieu :</label>
                <input name="lieuecole" id="lieu" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($lieuecole )?>" />
            </p>
            <p>
                <label for="telecole">Telephone :</label>
                <input name="telecole" id="telecole" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($telecole )?>" />
            </p>
            <p>
                <label for="signature1">Signature 1 :</label>
                <input name="signature1" id="signature1" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($signature1)?>" />
            </p>
            <p>
                <label for="signature2">Signature 2 :</label>
                <input name="signature2" id="signature2" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($signature2 )?>" />
            </p>
            <p>
                <label for="signature3">Signature 3 :</label>
                <input name="signature3" id="signature3" size="30" maxlength="50" type="text" value="<?php echo Html::Encode($signature3 )?>" />
            </p>

            <input name="Submit" value="Enregistrer" type="submit"><br/><br/> 
    </form>
<?php View::EndBlock()?>

<?php View::Render("layout.inc.php")?>
