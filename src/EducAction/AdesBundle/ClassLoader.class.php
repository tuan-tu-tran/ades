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

class ClassLoader
{
    private $root;
    private $mapping;
    public function __construct($root, $array)
    {
        $this->root = $root;
        $this->mapping=$array;
    }
    
    public function loadClass($classname)
    {
        if (Tools::TryGet($this->mapping, $classname, $file)) {
            $file=$this->root."/$file";
            if (!file_exists($file)) {
                throw new Exception("could not load class $classname : file $file does not exist");
            }

            require $file;
        }
    }

    public function Register()
    {
        spl_autoload_register(array($this,"loadClass"));
    }
}

