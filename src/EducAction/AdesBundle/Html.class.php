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

class Html{
	public static function Script($source){
		echo "<script type='text/javascript' src='$source'></script>";
	}

	public static function Css($href){
		echo "<link rel='stylesheet' href='$href' type='text/css' />";
	}

    /**
     * A short hand to call htmlspecialchars with custom default values:
     * * ENT_QUOTES|ENT_HTML401
     * * ISO8859-1
     * * double_encode = TRUE
     */
    public static function Encode($string, $flags = ENT_QUOTES, $encoding="ISO8859-1", $double_encode=TRUE)
    {
        return htmlspecialchars($string, $flags, $encoding, $double_encode);
    }
}
