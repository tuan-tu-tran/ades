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

class Detention
{
    public $id;
    public $occupancy;
    public $capacity;
    public $duration;
    public $date;
    public $time;

    private static $mapping=array(
        "idretenue"=>"id",
        "places"=>"capacity",
        "occupation"=>"occupancy",
        "duree"=>"duration",
        "heure"=>"time",
        "ladate"=>"date",
    );

    const SELECT_FIELDS=" idretenue, places, occupation, duree, heure, ladate ";

    public static function getVisibleDates($detentionType)
    {
        $result = Db::GetInstance()->query("
            SELECT ".self::SELECT_FIELDS."
            FROM ades_retenues
            WHERE typeDeRetenue = ?
            AND affiche = 'O'
            ORDER BY ladate
        ", $detentionType);
        return Tools::orm($result, get_class(), self::$mapping);
    }

    /**
     * Create a Detention instance for the given id
     *
     * @param int $id the id of the detention to get
     * @return Detention the instance or NULL if not found
     */
    public static function GetById($id)
    {
        $result = Db::GetInstance()->query("SELECT ".self::SELECT_FIELDS." FROM ades_retenues WHERE idretenue = ?", $id);
        if(count($result) > 0){
            return Tools::ormOne($result[0], get_class(), self::$mapping);
        } else {
            return NULL;
        }
    }

    public function __get($name)
    {
        $methodname = "get$name";
        return $this->$methodname();
    }

    public function getFreePlaces()
    {
        return $this->capacity - $this->occupancy;
    }
}

