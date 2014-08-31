<?php
/**
 * Copyright (c) 2014 Educ-Action
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

class Label
{
    public static function GetForFact($factId) {
        $factId = intval($factId);
        error_log($factId);
        if($factId <= 0) {
            return array();
        } else {
            $db=Db::GetInstance();
            $result=$db->query(
                "SELECT lbl_tag ".
                "FROM ades_labels ".
                "JOIN ades_fact_label ON fl_lbl_id = lbl_id ".
                "WHERE fl_fact_id = $factId ".
                "AND lbl_deleted = 0 ".
            "");
            $labels=array();
            foreach($result as $row){
                $labels[] = $row[0];
            }
            return $labels;
        }
    }

    public static function GetAll() {
        $db=Db::GetInstance();
        $result=$db->query(
            "SELECT lbl_tag ".
            "FROM ades_labels ".
            "WHERE lbl_deleted = 0".
        "");
        $labels=array();
        foreach($result as $row){
            $labels[] = $row[0];
        }
        return $labels;
    }
}

