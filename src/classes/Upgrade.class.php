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

class Upgrade{
	const Version="0.0";
	public function parseRequest(){
		$this->ShowVersionAction();
	}
	private function ShowVersionAction(){
		$this->fromVersion = self::GetDbVersion();
		$this->toVersion = self::Version;
		View::Render("Upgrade/index.inc.php", $this);
	}

	private static function GetDbVersion(){
		//get the version from db
		if(!Db::GetInstance()->scalar("SHOW TABLES LIKE 'ades_config'")){
			$db_version="0.0";
		}else{
			$db_version=Config::Get("db_version");
			if(!$db_version){
				throw new Exception("could not determine db version: table ades_config exists but no value for db_version");
			}
		};
		return $db_version;
	}
	public static function UpgradeDb(){
		var_dump($db_version);
	}
}
