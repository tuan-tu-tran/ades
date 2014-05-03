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
		$this->GetVersions();
		View::Render("Upgrade/index.inc.php", $this);
	}

	private function GetVersions(){
		$this->fromVersion = self::GetDbVersion();
		$this->toVersion = self::Version;
		$this->fromBeforeTo = self::CompareVersions($this->fromVersion, $this->toVersion)==-1;
		if($this->fromBeforeTo){
			$upgradeScripts=Path::ListDir(self::UpgradeFolder(), "/^to\d+\.\d+\.sql$/");
			usort($upgradeScripts, function ($x,$y){
				$vx=self::GetScriptVersion($x);
				$vy=self::GetScriptVersion($y);
				return self::CompareVersions($vx,$vy);
			});
			$scriptsToExecute=array();
			foreach($upgradeScripts as $script){
				$scriptVersion=self::GetScriptVersion($script);
				if(
					self::CompareVersions($scriptVersion, $this->fromVersion)>0
					&& 
					self::CompareVersions($scriptVersion, $this->toVersion)<=0
				){
					$scriptsToExecute[]=$script;
				}
			}
			$this->upgradeScripts=$upgradeScripts;
			$this->scriptsToExecute=$scriptsToExecute;
		}
	}

	private static function UpgradeFolder(){
		return DIRNAME(__FILE__)."/../upgrade/";
	}

	private static function CompareVersions($x,$y){
		list($majx,$minx)=explode(".",$x);
		list($majy,$miny)=explode(".",$y);
		if($majx<$majy) return -1;
		else if($majx>$majy) return 1;
		else if($minx<$miny) return -1;
		else if($minx>$miny) return 1;
		else return 0;
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

	private static function GetScriptVersion($script){
		return str_replace("to","",str_replace(".sql","",$script));
	}
}
