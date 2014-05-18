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

namespace EducAction\AdesBundle\Controller;

use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\Menu;
use EducAction\AdesBundle\Config;

class StudentFile
{
    public static function CreateMenu($ideleve, &$errors)
    {
        $prototypeFaits = new \prototypeFait();
        $listeTitres = $prototypeFaits->tableauTitresFaits();

        //read the facts menu composition: key is group name, value is comma separated fact ids
        $groupsConfigFile=Config::ConfigFile("menu_facts.ini");
        if (file_exists($groupsConfigFile)) {
            $menuConfig=parse_ini_file($groupsConfigFile, TRUE);
        } else {
            $menuConfig=array();
        }
        $groups=Tools::GetDefault($menuConfig,"composition", array());
        $styleConfig=Tools::GetDefault($menuConfig,"style", array());
        $order=Tools::GetDefault($styleConfig, "order", array());
        if ($order) {
            $order=explode(",",$order);
        }

        $factInfoById=array();
        foreach ($listeTitres as $unTitre) {
            $id = $unTitre['id_TypeFait'];
            $title = $unTitre['titreFait'];
            $link = "fait.php?mode=nouveau&ideleve=$ideleve&type=$id";
            $factInfoById[$id]=array(
                "title"=>$title,
                "link"=>$link,
            );
        }

        $tree=array();
        $errors=[];
        foreach($order as $item){
            $item=trim($item);
            if (isset($groups[$item])) {
                self::AddGroupToMenuTree($tree, $item, $groups[$item], $factInfoById, $errors);
                unset($groups[$item]);
            } elseif (isset($factInfoById[$item])) {
                $factInfo=$factInfoById[$item];
                $tree[$factInfo["title"]]=$factInfo["link"];
                unset($factInfoById[$item]);
            } else {
                $errors[]="L'ordre contient une valeur non-reconnue: '$item'.";
            }
        }

        foreach($groups as $groupName=>$facts) {
            self::AddGroupToMenuTree($tree, $groupName, $facts, $factInfoById, $errors);
        }

        foreach($factInfoById as $id=>$factInfo) {
            $tree[$factInfo["title"]]=$factInfo["link"];
        }

        $menu=new Menu("menu_facts");
        $menu->SetTree($tree);

        //set the menu style
        $menu->horizontalSpacing=Tools::GetDefault($styleConfig, "horizontal-spacing", "5px");
        return $menu;
    }

    private static function AddGroupToMenuTree(&$tree, $groupName, $facts, &$factInfoById, &$errors)
    {
        $array=array();
        foreach (explode(",",$facts) as $factId) {
            $factId=trim($factId);
            if ($factInfo=Tools::GetDefault($factInfoById, $factId, NULL)) {
                $array[$factInfo["title"]]=$factInfo["link"];
                unset($factInfoById[$factId]);
            } else {
                $errors[]="Le groupe '$groupName' contient un identifiant de fait non reconnu '$factId'.";
            }
        }
        if ($array) {
            $tree[$groupName] = $array;
        }
    }

}
