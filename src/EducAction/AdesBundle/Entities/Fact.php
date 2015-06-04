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

use EducAction\AdesBundle\Tools;

class Fact
{
    private $dbRow=array();

    public static function GetNew($typeId, $studentId, $userId)
    {
        $f=new Fact();
        $f->dbRow["ladate"] = date("Y-m-d");
        $f->dbRow["idfait"] = -1;
        $f->dbRow["type"] = $typeId;
        $f->dbRow["ideleve"] = $studentId;
        $f->dbRow["qui"] = $userId;
        return $f;
    }

    public function getValue(PrototypeField $field)
    {
        return Tools::GetDefault($this->dbRow, $field->name);
    }
}
