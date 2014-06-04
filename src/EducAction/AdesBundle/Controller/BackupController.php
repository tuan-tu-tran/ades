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

namespace EducAction\AdesBundle\Controller;

use EducAction\AdesBundle\User;
use EducAction\AdesBundle\Path;
use EducAction\AdesBundle\FlashBag;
use EducAction\AdesBundle\View;
use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Process;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\Utils;
use EducAction\AdesBundle\Config;
use \DateTime;
use \SplFileInfo;

class BackupController extends Controller
{
	const regex="/^\d{8}-\d{6}\.sql$/";
	public function parseRequest(){
		User::CheckIfLogged();
		User::CheckAccess("admin");
		$action=isset($_GET["action"])?$_GET["action"]:NULL;
		switch($action){
			case "create":
				$this->backupAction();
				break;
			case "delete":
				if(isset($_GET["file"])){
					$this->deleteAction($_GET["file"]);
					break;
				}
			case "restore":
				if(isset($_GET["file"])){
					$this->restoreAction($_GET["file"]);
					break;
				}
			case "download":
				if(isset($_GET["file"])){
					$this->downloadAction($_GET["file"]);
					break;
				}
			default:
				$this->listAction();
		}
	}

    public function restoreAction($file)
    {
		if(preg_match(self::regex, $file)){
            $restore=$this->params;
			$restore->filename=$file;
			if($input=file_get_contents(self::BackupFolder()."/".$file)){
				$restore->input_read=true;
                //drop all the tables first
                $db=Db::GetInstance();
                if ($db->TryQuery("SHOW TABLES", $result, $restore->error)) {
                    $dropped=TRUE;
                    foreach($result as $row){
                        $tableName=$row[0];
                        if (!$db->TryExecute("DROP TABLE $tableName", $restore->error)) {
                            $dropped=FALSE;
                            break;
                        }
                    }
                } else {
                    $dropped=FALSE;
                };
                if (!$dropped) {
                    $restore->failed=TRUE;
                    $restore->launched=TRUE;
                }else
				if(Utils::MySqlScript($input, $err,$launched)){
					$restore->failed=false;
					$restore->launched=true;
				}else{
					$restore->failed=true;
					$restore->launched=$launched;
					if($restore->failed && $restore->launched)
						$restore->error=$err;
				}
			}else{
				$restore->failed=true;
				$restore->input_read=false;
				$restore->error=Tools::GetLastError();
			}
            $this->flash()->set("restore",$restore);
		}
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
	}

	private function deleteAction($filename){
		if(preg_match(self::regex, $filename)){
            $fullname=self::BackupFolder()."/".$filename;
            $infoname=self::GetInfoFilename($fullname);
			if(unlink($fullname) && unlink($infoname)){
				$this->failed=false;
			}else{
				$this->failed=true;
				$this->error=Tools::GetLastError();
			}
			$this->filename=$filename;
			FlashBag::Set("delete",$this);
			Tools::Redirect("sauver.php");
		}else{
			Tools::Redirect("sauver.php");
		}

	}

    public function indexAction()
    {
		$list=Path::ListDir(self::BackupFolder(), self::regex );
		rsort($list);
		$files=array();
		$now=new DateTime();
		foreach($list as $file){
			$path=self::BackupFolder()."/".$file;
			$info=new SplFileInfo($path);
			$mtime=new DateTime("@".$info->getMTime());
			$mtime->setTimezone($now->getTimezone());
            $backupInfo=unserialize(file_get_contents(self::GetInfoFilename($path)));
			$files[]=array(
				"download_link"=>"?action=download&file=$file",
				"name"=>$file,
				"time"=>$mtime,
				"size"=>$info->getSize(),
                "version"=>$backupInfo["version"],
                "is_current_version"=>$backupInfo["version"]==Upgrade::Version,
                "comment"=>$backupInfo["comment"],
			);
		}
        $params=$this->params;
		$params->backup_files=$files;

		if(count($list)>0){
			$params->last_backup=$list[0];
			$utc_backup_time = new DateTime("@".filemtime(self::BackupFolder()."/".$params->last_backup));
			$now=new DateTime();
			$utc_backup_time->setTimezone($now->getTimezone());
			$params->last_backup_time=$utc_backup_time;
			$diff=$now->diff($params->last_backup_time);
			$params->last_backup_since=$diff;
		}

		$params->backup=$this->flash()->get("backup");

		$params->delete=$this->flash()->get("delete");

		$params->restore=$this->flash()->get("restore");

		return $this->View("index.html.twig");
	}

	public function backupAction(){
        $result=$this->params;
        $comment=$_POST["backup_create_comment_set"]?$_POST["backup_create_comment"]:"";
		$db=Db::GetInstance();
		$host=$db->host;
		$username=$db->username;
		$pwd=$db->pwd;
		$dbname=$db->dbname;
		$cmd="mysqldump --host=$host --user=$username --password=$pwd $dbname";
		if(Process::Execute($cmd, NULL, $out, $err, $retval)){
			$result->dump_launched=true;
			if($retval==0){
				$filename=date('Ymd-His').".sql";
				$fullpath = self::BackupFolder()."/".$filename;
                $fullInfoPath=self::GetInfoFilename($fullpath);
                $info=array(
                    "filename"=>$filename,
                    "version"=>Config::GetDbVersion(),
                    "comment"=>$comment,
                );
				if(file_put_contents($fullpath, $out) && file_put_contents($fullInfoPath, serialize($info))){
					$result->filename=$filename;
					$result->failed=false;
				}else{
					$result->failed=true;
					$result->error=error_get_last()["message"];
				}
			}
			else{
				$result->failed=true;
				$result->error=$err;
			}
		}
		else{
			$result->failed=true;
			$result->dump_launched=false;
		}
		$this->flash()->set("backup", $result);
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
	}

    private static function GetInfoFilename($sqlFilename)
    {
        return substr_replace($sqlFilename,"txt", -3);
    }

	private static function BackupFolder()
	{
		return DIRNAME(__FILE__)."/../../../../local/db_backup";
	}

	private function downloadAction($filename)
	{
		if(preg_match(self::regex, $filename)){
			$path=self::BackupFolder()."/$filename";
			if(file_exists($path)){
				$content=file_get_contents($path);
				if($content!==FALSE){
					header("Content-Type: application/x-sql");
					echo $content;
				}
			}
		}
	}
}
