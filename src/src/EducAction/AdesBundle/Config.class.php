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
		return Tools::TryGet(self::$config, $key, $default);
	}

	public static function Set($key, $value){
		$db=Db::GetInstance();
		$query=
			"INSERT INTO ades_config(con_key, con_value) "
			." VALUES ('%s', '%s') "
			." ON DUPLICATE KEY UPDATE "
			." con_value = '%s' "
		;
		$query=sprintf($query
			, $db->escape_string($key)
			, $db->escape_string($value)
			, $db->escape_string($value)
		);
		if($db->execute($query)){
			if(self::$config){
				self::$config[$key]=$value;
			}
			return true;
		}else{
			throw new Exception("could not set config value '$key' to '$value' : ".(Db::GetInstance()->error()));
		}
	}

	public static function SetDbVersion($version){
		return self::Set("db_version",$version);
	}

	public static function GetDbVersion(){
		return self::Get("db_version");
	}
}
