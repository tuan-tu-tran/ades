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
namespace EducAction\AdesBundle;
?>

<?php View::FillBlock("title", "ADES - Erreur")?>
<?php View::StartBlock("body")?>
    <h1>Erreur d'accès à la base de données</h1>

<div id="texte">
    <fieldset class="notice">
        <legend>Erreur</legend>
        <p class="impt">Il y a eu une erreur d'accès à la base données.</p>
        <p>Veuillez réessayer plus tard ou si le problème persiste, contacter l'administrateur.</p>
    </fieldset>
    <?php if(isset($back) && $back):?>
        <p><a href="javascript:history.go(-1)">Retourner à la page précédente</a></p>
    <?php endif ?>
</div>

<?php View::EndBlock()?>

<?php View::Render("base.inc.php")?>



