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
use EducAction\AdesBundle\Db;

class Fact
{
    public $prototypeId=-1;
    private $dbRow=array();

    public static function GetById($id)
    {
        $f=new Fact();
        $result=Db::GetInstance()->query("SELECT * FROM ades_faits WHERE idfait=?", $id);
        if(count($result)>0){
            $f->dbRow=&$result[0];
            $f->prototypeId=$f->dbRow["type"];
            return $f;
        } else {
            return NULL;
        }
    }

    public function getStudent()
    {
        $studentId=$this->dbRow["ideleve"];
        $student = Student::GetById($studentId);
        if(!$student){
            throw new \Exception("could not fetch student with id $studentId");
        }
        return $student;
    }
    public static function GetNew($typeId, $studentId, $userId)
    {
        $f=new Fact();
        $f->dbRow["ladate"] = date("Y-m-d");
        $f->dbRow["idfait"] = -1;
        $f->dbRow["type"] = $typeId;
        $f->prototypeId=$typeId;
        $f->dbRow["ideleve"] = $studentId;
        $f->dbRow["qui"] = $userId;
        return $f;
    }

    public function getValue(PrototypeField $field)
    {
        return Tools::GetDefault($this->dbRow, $field->name);
    }
}
