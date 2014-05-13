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

class Menu
{
    private $tree=array();

    public function SetTree($tree)
    {
        $this->tree=$tree;
    }

    public function RenderBody()
    {
        View::Render("Menu/menu.inc.php", array("tree"=>$this->tree));
    }

    public function RenderHead($return=FALSE)
    {
        Html::Css("css/menu.css");
        Html::Script("js/menu.js");
    }
}
