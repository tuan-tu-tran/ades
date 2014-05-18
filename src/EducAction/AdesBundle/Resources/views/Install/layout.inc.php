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
?>

<?php View::FillBlock("title","Installation d'ADES")?>

<?php View::StartBlock("post_head")?>
    <style type="text/css">
        a:hover{text-decoration:underline;}
        label{width:10em;}
    </style>
    <?php View::Block("post_head")?>
<?php View::EndBlock()?>

<?php View::StartBlock("body")?>
    <div id="texte">
        <h2>Installation d'ADES</h2>
        <?php View::Block("content")?>
    </div>
<?php View::EndBlock()?>

<?php View::Render("base.inc.php")?>
