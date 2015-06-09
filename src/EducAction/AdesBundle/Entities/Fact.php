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
    /**
     * Return a new empty fact of a type authored by a user
     * and optionally associated with a student
     *
     * Some fields, common to all prototypes are initialized with default values:
     * <ul>
     *  <li>the date is set to today</li>
     *  <li>the id is set to -1</li>
     *  <li>the type id is initialized with the given value</li>
     *  <li>the user id is initialized with the given value</li>
     *  <li>the student id is initialized with the given value (-1 by default)</li>
     * </ul>
     *
     * @param int $typeId the id of fact's prototype
     * @param int $userId the id of the user that creates the fact
     * @param int $studentId the optional id of the student associated to the fact
     * @return Fact an empty fact instance.
     */
    public static function GetNew($typeId, $userId, $studentId=-1)
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

    /**
     * Set the value for the detention id field
     *
     * @param int $id the id to set
     * @return void
     */
    public function setDetentionId($id)
    {
        $this->dbRow["idretenue"]=$id;
    }
}
