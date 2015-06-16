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
		return Tools::GetDefault(self::$config, $key, $default);
	}

	public static function Set($key, $value){
		$db=Db::GetInstance();
		$query=
			"INSERT INTO ades_config(con_key, con_value) "
			." VALUES (%s, %s) "
			." ON DUPLICATE KEY UPDATE "
			." con_value = %s "
		;
		$query=sprintf($query
			, $db->escape_string($key)
			, $db->escape_string($value)
			, $db->escape_string($value)
		);
        $db->execute($query);
        if(self::$config){
            self::$config[$key]=$value;
        }
	}

	public static function SetDbVersion($version){
		self::Set("db_version",$version);
	}

	public static function GetDbVersion(){
		//get the version from db
		if(!Db::GetInstance()->scalar("SHOW TABLES LIKE 'ades_config'")){
			$db_version="0.0";
		}else{
			$db_version=self::Get("db_version");
			if(!$db_version){
				throw new Exception("could not determine db version: table ades_config exists but no value for db_version");
			}
		};
		return $db_version;
	}

    private static $schoolConfig=NULL;
    public static function getSchoolConfig($key)
    {
        if(self::$schoolConfig===NULL) {
            $fname=self::SchoolConfigFile();
            if(file_exists($fname)) {
                require_once $fname;
                self::$schoolConfig=array(
                    "name"=>mb_convert_encoding(ECOLE, "utf8","latin1"),
                    "title"=>mb_convert_encoding(TITRE, "utf8","latin1")
                );
            } else {
                self::$schoolConfig=array(
                    "name"=>"ECOLE",
                    "title"=>"TITRE"
                );
            }
        }
        return self::$schoolConfig[$key];
    }

	public static function SchoolConfigFile()
	{
		return DIRNAME(__FILE__)."/../../../local/constantes.inc.php";
	}

	public static function DbConfigFile()
	{
		return DIRNAME(__FILE__)."/../../../local/confbd.inc.php";
	}

    public static function LocalFile($file)
    {
		return DIRNAME(__FILE__)."/../../../local/$file";
    }

    public static function ConfigFile($file)
    {
		return DIRNAME(__FILE__)."/../../../config/$file";
    }

    public static function WebFile($file)
    {
		return DIRNAME(__FILE__)."/../../../web/$file";
    }

    /**
     * Return the symfony framework secret parameter from local/config.yml
     */
    public static function GetSecret()
    {
        $config=\Symfony\Component\Yaml\Yaml::Parse(self::LocalFile("config.yml"));
        return $config["parameters"]["secret"];
    }
}
