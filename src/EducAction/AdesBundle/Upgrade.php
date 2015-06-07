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

    /**
     * Return whether a db upgrade is required
     *
     * If this method returns FALSE, it means the code is allowed to run with the current db without any change.
     * This method returns TRUE if:
     * <ul>
     *  <li>the version of the code is higher than the version of the db, in which case a db upgrade needs to happen</li>
     *  <li>otherwise if the version of the code is incompatible with the version of the db (difference in major), in which case:
     *      <ul>
     *          <li>a compatible backup must be restored</li>
     *          <li>or the code needs to be upgraded</li>
     *      </ul>
     *  </li>
     *</ul>
     *
     * @return bool TRUE if an upgrade needs to happen or if the code is incompatible with db, FALSE otherwise i.e. code is allowed to run with the current db
     */
    public static function Required()
    {
        $compatible = self::IsCompatible(Config::GetDbVersion(), $upgradeRequired);
        if($compatible){
            return $upgradeRequired;
        }else{
            return TRUE;
        }
    }

    /**
     * Returns whether the given db version is compatible with the current code version, possibly requiring an upgrade.
     *
     * If this method returns TRUE, then:
     * <ul>
     *  <li>either the given version is lower than the code version, in which case an upgrade will need to happen and $upgradeRequired will be TRUE</li>
     *  <li>or the given version is higher than the code version, but with the same major, meaning the code is allowed to run with it, in which case $upgradeRequired will be FALSE</li>
     * </ul>
     * In either case, the code will be able to handle this db version.
     * Otherwise, it means the code will not be able to run with this db and either the code must be upgraded or the db rolled back to a compatible version.
     *
     * @param string $dbVersion the version whose compatibility must be assessed
     * @param bool &$upgradeRequired if TRUE is returned, this will be set to whether an upgrade is required for this version, otherwise this will be meaningless
     * @return bool whether the code is allowed to run with the given db version
     */
    public static function IsCompatible($dbVersion, &$upgradeRequired)
    {
        $codeVersion = self::Version;
        if($dbVersion==$codeVersion){
            $upgradeRequired = FALSE;
            return TRUE;
        }else if(self::CompareVersions($codeVersion, $dbVersion) > 0){
            //code > db => upgrade
            $upgradeRequired = TRUE;
            return TRUE;
        }else{
            //code < db : compatible if same major
            $codeMajor = self::GetMajor($codeVersion);
            $dbMajor = self::GetMajor($dbVersion);
            $upgradeRequired = FALSE;
            return $codeMajor == $dbMajor;
        }
        throw new \Exception("Upgrade::IsCompatible: unhandled case code:$codeVersion vs db:$dbVersion");
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

    /**
     * Return the major of a version
     *
     * The first part of X.Y(.Z.T...) i.e. X
     *
     * @param string $v a version that should be like X.Y but X.Y.Z... is also accepted
     * @return string X in X.Y....
     */
    private static function GetMajor($v)
    {
        return explode(".",$v)[0];
    }

    /**
     * Compare two Maj.Min versions
     *
     * Return an int < 0 if $x<$y, 0 if $x == $y and >0 if $x > $y.
     * First Maj is compared and in case of equality, Min is compared.
     *
     * @param string $x the first version that must conform to Maj.Min pattern
     * @param string $y the second version that must conform to Maj.Min pattern
     * @return int the integer result of the comparison
     */
    private  static function CompareVersions($x,$y)
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
