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

use EducAction\AdesBundle\Path;
use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Process;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\Utils;
use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\Backup;
use \DateTime;

class BackupController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return array("admin");
    }

    public function restoreAction($file)
    {
        if(Backup::isLegalFile($file)) {
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

    public function deleteAction($file)
    {
        if(Backup::isLegalFile($file)) {
            $delete=$this->params;
            $fullname=self::BackupFolder()."/".$file;
            $infoname=self::GetInfoFilename($fullname);
			if(unlink($fullname) && unlink($infoname)){
				$delete->failed=false;
			}else{
				$delete->failed=true;
				$delete->error=Tools::GetLastError();
			}
			$delete->filename=$file;
			$this->flash()->set("delete",$delete);
		}
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
	}

    public function indexAction()
    {
		$list=Backup::getFiles();
		$files=array();
		$now=new DateTime();
		foreach($list as $file){
			$path=Backup::getFolder()."/".$file;
			$info=new SplFileInfo($path);
			$mtime=new DateTime("@".$info->getMTime());
			$mtime->setTimezone($now->getTimezone());
            $backupInfo=unserialize(file_get_contents(self::GetInfoFilename($path)));
			$files[]=array(
				"name"=>$file,
				"time"=>Tools::GetDefault($backupInfo, "timestamp", $mtime),
				"size"=>$info->getSize(),
                "version"=>$backupInfo["version"],
                "is_current_version"=>$backupInfo["version"]==Upgrade::Version,
                "comment"=>$backupInfo["comment"],
			);
		}
        $params=$this->params;
		$params->backup_files=$files;

		if(count($files)>0){
			$params->last_backup=$files[0]["name"];
			$params->last_backup_time=$files[0]["time"];
			$diff=$now->diff($params->last_backup_time);
			$params->last_backup_since=$diff;
		}

		$params->backup=$this->flash()->get("backup");

		$params->delete=$this->flash()->get("delete");

		$params->restore=$this->flash()->get("restore");

		$params->upload=$this->flash()->get("upload");

		return $this->View("index.html.twig");
	}

    public function backupAction()
    {
        $result=$this->params;
        $comment=$_POST["comment_set"]?$_POST["comment"]:"";
		$db=Db::GetInstance();
		$host=$db->host;
		$username=$db->username;
		$pwd=$db->pwd;
		$dbname=$db->dbname;
		$cmd="mysqldump --host=$host --user=$username --password=$pwd $dbname";
		if(Process::Execute($cmd, NULL, $out, $err, $retval)){
			$result->dump_launched=true;
			if($retval==0){
                $info=array(
                    "version"=>Config::GetDbVersion(),
                    "timestamp"=>new DateTime(),
                );
                $content="-- info: ".serialize($info)."\n$out";
                $signature=$this->sign($content);
                $content="-- signature: $signature\n".$content;
                $filename=$this->saveBackup($content, $info, $comment);
                $result->filename=$filename;
                $result->failed=false;
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

    private function sign($content)
    {
        return hash_hmac("sha1", $content, $this->container->getParameter("secret"));
    }

    private function saveBackup($content, $info, $comment)
    {
        $info["comment"]=$comment;
        $timestamp=$info["timestamp"];
        $filename=$timestamp->format("Ymd-His").".sql";
        $fullpath = self::BackupFolder()."/".$filename;
        $fullInfoPath=self::GetInfoFilename($fullpath);
        file_put_contents($fullpath, $content);
        file_put_contents($fullInfoPath, serialize($info));
        return $filename;
    }

    public function uploadAction()
    {
        $request=$this->getRequest();
        $result=$this->params;
        $file=$request->files->get("upload");

        $result->success=FALSE;
        $post=$request->request;
        $comment=$post->get("comment_set")?($post->get("comment")):"";
        $result->comment=$comment;
        if($result->upload_found = ($file!= NULL ) ) { 
            if($result->uploaded = $file->isValid()){
                //check the signature
                $content=file_get_contents($file->getRealPath());
                if ($result->signature_found = preg_match("/^-- signature: ([0-9a-f]{40})\n/", $content, $matches)) {
                    $content=substr($content, strlen($matches[0]));
                    $signature=$this->sign($content);
                    if($result->signature_valid = ($signature==$matches[1]) ){
                        if($result->info_found = preg_match("/^-- info: ([^\n]+)\n/", $content, $matches)) {
                            $info = unserialize($matches[1]);
                            $filename=$this->saveBackup($content, $info, $comment);
                            $result->success=TRUE;
                            $result->filename=$filename;
                        }
                    }
                }
            } else {
                $result->error=$file->getError();
            }
        }
        $this->flash()->set("upload", $result);
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
    }

    private static function GetInfoFilename($sqlFilename)
    {
        return Backup::getInfoFilename($sqlFilename);
    }

	private static function BackupFolder()
	{
        return Backup::getFolder();
	}

	public function downloadAction($file)
	{
        if(Backup::isLegalFile($file)) {
			$path=self::BackupFolder()."/$file";
			if(file_exists($path)){
				$content=file_get_contents($path);
				if($content!==FALSE){
                    //TODO: sign the content if needed
                    //TODO: add the timestamp if needed
                    $response=new \Symfony\Component\HttpFoundation\Response($content);
                    $response->headers->set("Content-Type", "application/x-sql");
                    $response->headers->set("Content-Disposition", "attachment; filename=\"$file\"");
                    return $response;
				}
			}
		}
	}
}
