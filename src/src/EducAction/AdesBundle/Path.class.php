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

class Path{
	public static function ListDir($path, $regex_match=NULL, $exclude=array("index.php")){
		$liste1 = scandir($path,0);
		$liste2=array();
		foreach ($liste1 as $fichier){
			if (($fichier != '.') && ($fichier !='..')){
				if(!$regex_match || preg_match($regex_match, $fichier)){
					if(!in_array($fichier, $exclude)){
						$liste2[] = $fichier;
					}
				}
			}
		}
		return $liste2;
	}
}
