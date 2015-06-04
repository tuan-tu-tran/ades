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

class FactPrototype
{
    public $backgroundColor;
    public $textColor;
    public $focus;
    /**
     * @var string $title The fact title encoded in utf8
     */
    public $title;
    public $detentionType;
    public $fields=array();

    /**
     * Return the FactPrototype that corresponds to the given id
     *
     * @param int $id the fact prototype id
     * @param string $context the context filter for the fields
     * @return FactPrototype the prototype or NULL if not found
     */
    public static function GetById($id, $context)
    {
        $repo = new \prototypeFait();
        $data = $repo->descriptionFaitId($id);
        $prototype = new FactPrototype();
        $prototype->backgroundColor = "#".$data["couleurFond"];
        $prototype->textColor = "#".$data["couleurTexte"];
        $prototype->title = utf8_encode($data["titreFait"]);
        $prototype->detentionType = $data["typeDeRetenue"];
        $prototype->focus=Tools::GetDefault($data, "focus");
        $fields = $repo->detailDesChampsPourContexte($id, $context);
        foreach($fields as $data) {
            $prototype->fields[] = new PrototypeField($data);
        }
        return $prototype;
    }
}

