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

class Config{
	private static $config=NULL;
	public static function Get($key, $default=NULL){
		if(self::$config===NULL){
			$result=Db::GetInstance()->query("SELECT con_key, con_value FROM ades_config");
			self::$config=array();
			foreach($result as $row){
				self::$config[$row["con_key"]]=$row["con_value"];
			}
		}
	}
}
