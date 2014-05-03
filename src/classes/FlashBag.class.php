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

class FlashBag{
	const SESSION_KEY="ADES.flash";
	private static $bag=NULL;

	private static function &GetInstance(){
		if(self::$bag === NULL){
			if(!isset($_SESSION[self::SESSION_KEY])){
				$_SESSION[self::SESSION_KEY]=array();
			}
			self::$bag=&$_SESSION[self::SESSION_KEY];
		}
		return self::$bag;
	}

	public static function Pop($key, $default=NULL){
		$bag=&self::GetInstance();
		if(isset($bag[$key])){
			$value=$bag[$key];
			unset($bag[$key]);
		}else{
			$value=$default;
		}
		return $value;
	}

	public static function Set($key, $value){
		self::GetInstance()[$key]=$value;
	}
}
