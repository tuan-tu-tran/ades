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

class Backup{
	const root="sauvegarde";
	public function parseRequest(){
		User::CheckIfLogged();
		User::CheckAccess("admin");
		$action=isset($_GET["action"])?$_GET["action"]:NULL;
		switch($action){
			case "create":
				$this->backupAction();
				break;
			default:
				$this->listAction();
		}
	}
	private function View($template,$params=NULL){
		if($params==NULL) $params = $this;
		View::Render("Backup/$template", $params);
	}

	private function listAction(){
		$list=Path::ListDir(Backup::root);
		$files=array();
		foreach($list as $file){
			$files[]=array(
				"path"=>self::root."/".$file,
				"name"=>$file
			);
		}
		$this->backup_files=$files;

		rsort($list);
		if(count($list)>0){
			$this->last_backup=$list[0];
			$utc_backup_time = new DateTime("@".filemtime(Backup::root."/".$this->last_backup));
			$now=new DateTime();
			$utc_backup_time->setTimezone($now->getTimezone());
			$this->last_backup_time=$utc_backup_time;
			$diff=$now->diff($this->last_backup_time);
			$this->last_backup_since=$diff;
		}

		$this->backup=FlashBag::Pop("backup");

		$this->View("list.inc.php");
	}

	private function backupAction(){
		$inoutdesc=array(
			0=>array("pipe","r"),
			1=>array("pipe","w"),
			2=>array("pipe","w"),
		);
		$db=Db::GetInstance();
		$host=$db->host;
		$username=$db->username;
		$pwd=$db->pwd;
		$dbname=$db->dbname;
		if($proc=proc_open("mysqldump --host=$host --user=$username --password=$pwd $dbname",$inoutdesc, $pipes)){
			fclose($pipes[0]);
			$out=stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$err=stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			$retval=proc_close($proc);
			$this->dump_launched=true;
			if($retval==0){
				$filename=date('Ymd-His').".sql";
				$fullpath = self::root."/".$filename;
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
}
