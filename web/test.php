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
require "inc/init.inc.php";

use EducAction\AdesBundle\View;


$tree=array(
    "Entretiens"=>array(
        "Entretiens individuels"=>"javascript:console.log('1')",
        "Entretiens téléphoniques"=>"javascript:console.log('2')",
    ),
    "Retenues1"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues2"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    /*
    "Retenues3"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues4"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues17"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues16"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues15"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues14"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues13"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues12"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues11"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues10"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues9"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues8"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues7"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
     */
    "Retenues6"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Retenues5"=>array(
        "Retenues1 disciplinaires"=>"javascript:console.log('3')",
        "Retenues2 disciplinaires"=>"javascript:console.log('3')",
        "Retenues3 téléphoniques"=>"javascript:console.log('4')",
        "Retenues4 téléphoniques"=>"javascript:console.log('4')",
    ),
    "Félicitations"=>"javascript:console.log('5')",
);

$menu=new EducAction\AdesBundle\Menu;
$menu->SetTree($tree);

?>

<?php View::StartBlock("post_head")?>
<?php $menu->RenderHead();?>
<?php View::EndBlock()?>

<?php View::StartBlock("content")?>
<?php $menu->RenderBody();?>
<?php View::EndBlock()?>

<?php View::Embed("layout.inc.php")?>
