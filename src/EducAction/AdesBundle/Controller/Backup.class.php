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
use \DateTime;
use \SplFileInfo;

class Backup{
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
	private function restoreAction($filename){
		if(preg_match(self::regex, $filename)){
			$this->filename=$filename;
			if($input=file_get_contents(self::BackupFolder()."/".$filename)){
				$this->input_read=true;
				if(Utils::MySqlScript($input, $err,$launched)){
					$this->failed=false;
					$this->launched=true;
				}else{
					$this->failed=true;
					$this->launched=$launched;
					if($this->failed && $this->launched)
						$this->error=$err;
				}
			}else{
				$this->failed=true;
				$this->input_read=false;
				$this->error=Tools::GetLastError();
			}
			FlashBag::Set("restore",$this);
			Tools::Redirect("sauver.php");
		}else{
			Tools::Redirect("sauver.php");
		}
	}
	private function deleteAction($filename){
		if(preg_match(self::regex, $filename)){
			if(unlink(self::BackupFolder()."/".$filename)){
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

	private function View($template,$params=NULL){
		if($params==NULL) $params = $this;
		View::Render("Backup/$template", $params);
	}

	private function listAction(){
		$list=Path::ListDir(self::BackupFolder(), self::regex );
		rsort($list);
		$files=array();
		$now=new DateTime();
		foreach($list as $file){
			$path=self::BackupFolder()."/".$file;
			$info=new SplFileInfo($path);
			$mtime=new DateTime("@".$info->getMTime());
			$mtime->setTimezone($now->getTimezone());
			$files[]=array(
				"download_link"=>"?action=download&file=$file",
				"name"=>$file,
				"time"=>$mtime,
				"size"=>$info->getSize(),
			);
		}
		$this->backup_files=$files;

		if(count($list)>0){
			$this->last_backup=$list[0];
			$utc_backup_time = new DateTime("@".filemtime(self::BackupFolder()."/".$this->last_backup));
			$now=new DateTime();
			$utc_backup_time->setTimezone($now->getTimezone());
			$this->last_backup_time=$utc_backup_time;
			$diff=$now->diff($this->last_backup_time);
			$this->last_backup_since=$diff;
		}

		$this->backup=FlashBag::Pop("backup");

		$this->delete=FlashBag::Pop("delete");

		$this->restore=FlashBag::Pop("restore");

		$this->View("list.inc.php");
	}

	private function backupAction(){
		$db=Db::GetInstance();
		$host=$db->host;
		$username=$db->username;
		$pwd=$db->pwd;
		$dbname=$db->dbname;
		$cmd="mysqldump --host=$host --user=$username --password=$pwd $dbname";
		if(Process::Execute($cmd, NULL, $out, $err, $retval)){
			$this->dump_launched=true;
			if($retval==0){
				$filename=date('Ymd-His').".sql";
				$fullpath = self::BackupFolder()."/".$filename;
				if(file_put_contents($fullpath, $out)){
					$this->filename=$filename;
					$this->failed=false;
				}else{
					$this->failed=true;
					$this->error=error_get_last()["message"];
				}
			}
			else{
				$this->failed=true;
				$this->error=$err;
			}
		}
		else{
			$this->failed=true;
			$this->dump_launched=false;
		}
		FlashBag::Set("backup", $this);
		Tools::Redirect("sauver.php");
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
