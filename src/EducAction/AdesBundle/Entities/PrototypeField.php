<?php
/**
 * Copyright (c) 2015 Tuan-Tu Tran
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

namespace EducAction\AdesBundle\Entities;

class PrototypeField
{
    public $type;
    public $name;
    public $size;
    public $maxlength;
    public $cssClass;
    public $label;
    public $isDate;
    public $javascriptEvent;
    public $javascriptCommand;
    public $columns;
    public $rows;
    private $contexts;

    public function __construct(&$data)
    {
        $this->type = $data["typeChamp"];
        $this->name = $data["champ"];
        $this->size = $data["size"];
        $this->maxlength = $data["maxlength"];
        $this->cssClass = $data["classCSS"];
        $this->label = utf8_encode($data["label"]);
        $this->isDate =$data["typeDate"];
        $this->contexts = $data["contextes"];
        $this->javascriptEvent = $data["javascriptEvent"];
        $this->javascriptCommand = $data["javascriptCommand"];
        $this->contexts = $data["contextes"];
        $this->columns=$data["colonnes"];
        $this->rows=$data["lignes"];
    }
}

