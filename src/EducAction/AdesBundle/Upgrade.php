<?php
/**
 * Copyright (c) 2014 Educ-Action
 * Copyright (c) 2015 Tuan-Tu Tran
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

class Upgrade
{
	const Version="2.0";

	public static function Required(){
		return Config::GetDbVersion()!=self::Version;
	}

	public static function CheckIfNeeded(){
		if(self::Required()){
			Tools::Redirect("upgrade-db");
		}
	}

    public static function execute(&$result, $createBackup = TRUE)
    {
        $result=self::GetVersions();
        if($result->fromBeforeTo){
            $result->currentVersion = $result->fromVersion;
            $result->executedScripts=array();
            if ($createBackup) {
            //Create the backup
            $backup = Backup::createSigned("[auto]avant mise à jour db vers ".self::Version, $backupResult);
            $result->backup=$backupResult;
            }
            if(!$createBackup || $backup){
                foreach($result->scriptsToExecute as $script){
                    $content=file_get_contents(self::UpgradeFolder().$script);
                    if($content===FALSE){
                        $result->failedScript=$script;
                        $result->failedScriptError=Tools::GetLastError();
                        break;
                    }elseif(!Utils::MySqlScript($content, $err,$launched)){
                        $result->failedScript = $script;
                        if(!$err){
                            if($launched){
                                $err="mysql script launched but no error output returned";
                            }else {
                                $err="mysql script not launched and no error output";
                            }
                        }
                        $result->failedScriptError=$err;
                        break;
                    }else{
                        $result->executedScripts[]=$script;
                        $result->currentVersion = self::GetScriptVersion($script);
                        Config::SetDbVersion($result->currentVersion);
                    }
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public static function GetVersions()
    {
        $versions=new Bag();
        $versions->fromVersion = Config::GetDbVersion();
        $versions->toVersion = self::Version;
        $versions->fromBeforeTo = self::CompareVersions($versions->fromVersion, $versions->toVersion)==-1;
        if($versions->fromBeforeTo){
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
                    self::CompareVersions($scriptVersion, $versions->fromVersion)>0
                    &&
                    self::CompareVersions($scriptVersion, $versions->toVersion)<=0
                ){
                    $scriptsToExecute[]=$script;
                }
            }
            $versions->upgradeScripts=$upgradeScripts;
            $versions->scriptsToExecute=$scriptsToExecute;
        }
        return $versions;
    }

    private static function CompareVersions($x,$y)
    {
        list($majx,$minx)=explode(".",$x);
        list($majy,$miny)=explode(".",$y);
        if($majx<$majy) return -1;
        else if($majx>$majy) return 1;
        else if($minx<$miny) return -1;
        else if($minx>$miny) return 1;
        else return 0;
    }

    private static function UpgradeFolder()
    {
        return DIRNAME(__FILE__)."/Resources/sql_scripts/";
    }

    private static function GetScriptVersion($script)
    {
        return str_replace("to","",str_replace(".sql","",$script));
    }

}
