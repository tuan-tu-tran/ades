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

use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Tools;

class Student
{
    const SELECT_FIELDS="
        nom, prenom, classe, contrat, ideleve
        ";
    public $lastName;
    public $firstName;
    public $class;
    public $hasContract;
    public $id;

    private static $mapping=array(
        "nom"=>"lastName",
        "prenom"=>"firstName",
        "classe"=>"class",
        "contrat"=>"hasContract",
        "ideleve"=>"id",
    );

    private static $conversion=array(
        "contrat"=>array("EducAction\\AdesBundle\\Entities\\Student", "Boolean"),
        "nom"=>"utf8_encode",
        "prenom"=>"utf8_encode",
    );

    /**
     * Get a Student instance by it numeric $id
     *
     * @param int $id the numeric id
     * @return a Student instance or NULL if not found
     */
    public static function GetById($id)
    {
        $db=Db::GetInstance();
        $result=
        $db->query("SELECT ".self::SELECT_FIELDS."
            FROM ades_eleves
            WHERE ideleve = ?
            ", $id);
        if(count($result) > 0) {
            return self::FromDbRow($result[0]);
        } else {
            return NULL;
        }
    }

    public static function GetAll()
    {
        $query="SELECT ".self::SELECT_FIELDS." FROM ades_eleves";
        $result=Db::GetInstance()->query($query);
        return Tools::orm($result, get_class(), self::$mapping, self::$conversion);
    }

    private static function FromDbRow(array &$row)
    {
        $student=new Student();
        foreach(self::$mapping as $src=>$dst){
            $value = $row[$src];
            if(Tools::TryGet(self::$conversion, $src, $conversion)){
                $value = call_user_func($conversion, $value);
            }
            $student->$dst = $value;
        }
        return $student;
    }

    public static function Boolean($char)
    {
        return $char == "O";
    }
}
