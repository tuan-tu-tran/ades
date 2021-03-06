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

use EducAction\AdesBundle\Tools;

class ArrayWrapper
{
    private $array;
    public function __construct(Array &$array)
    {
        $this->array=&$array;
    }

    public function set($key, $value)
    {
        $this->array[$key]=$value;
    }

    public function get(string $key, $default=NULL)
    {
        return Tools::GetDefault($this->array, $key, $default);
    }

    public function tryGet(string $key, &$value, $default=NULL)
    {
        if(Tools::TryGet($this->array, $key, $value))
        {
            return TRUE;
        }
        $value=$default;
        return FALSE;
    }
}
