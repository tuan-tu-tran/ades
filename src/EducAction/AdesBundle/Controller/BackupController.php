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
use EducAction\AdesBundle\Bag;
use EducAction\AdesBundle\Upgrade;
use \DateTime;

class BackupController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return array("admin");
    }

    public function isPublicAction($action)
    {
        if($action == "downloadAction")
        {
            $atLogout = $this->flash()->get("atLogout");
            return $atLogout;
        }
    }

    public function restoreAction($file)
    {
        return Backup::Restore($file, $this);
	}

    public function deleteAction($file)
    {
        if($this->delete($file, $delete)){
			$this->flash()->set("delete",$delete);
        }
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
	}

    private function delete($file, &$delete)
    {
        if(Backup::isLegalFile($file)) {
            $delete = new Bag();
            $fullname=self::BackupFolder()."/".$file;
            $infoname=self::GetInfoFilename($fullname);
            try{
                $done = unlink($fullname) && unlink($infoname);
            }catch(\Exception $e){
                $done = FALSE;
                $delete->error = $e->getMessage();
            }
			$delete->filename=$file;
            $delete->failed = !$done;
            return TRUE;
		}
        return FALSE;
    }

    public function indexAction()
    {
        Upgrade::CheckIfNeeded();
		$files=Backup::getList();
        $params=$this->params;
		$params->backup_files=$files;

		if(count($files)>0){
            $now=new DateTime();
			$params->last_backup=$files[0]["name"];
			$params->last_backup_time=$files[0]["time"];
			$diff=$now->diff($params->last_backup_time);
			$params->last_backup_since=$diff;
		}

		$params->backup=$this->flash()->get("backup");

		$params->delete=$this->flash()->get("delete");

		$params->restore=$this->flash()->get("restore");

		$params->upload=$this->flash()->get("upload");

        $params->deleted_files = $this->flash()->get("deleted_files");

		return $this->View("index.html.twig");
	}

    public function backupAction()
    {
        $comment=$_POST["comment_set"]?$_POST["comment"]:"";
        Backup::create($comment, $this, $result);
		$this->flash()->set("backup", $result);
        return $this->redirect($this->generateUrl("educ_action_ades_backup"));
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
                    $signature=Backup::sign($content, $this);
                    if($result->signature_valid = ($signature==$matches[1]) ){
                        if($result->info_found = preg_match("/^-- info: ([^\n]+)\n/", $content, $matches)) {
                            $info = unserialize($matches[1]);
                            $filename=Backup::save($content, $info, $comment);
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

    public function deleteManyAction()
    {
        $request=$this->getRequest();
        $files=$request->request->get("to_delete");
        if(count($files) == 1){
            return $this->deleteAction($files[0]);
        } else {
        $deleted=new Bag();
        $deleted->successes=array();
        $deleted->failures=array();
        foreach($files as $file){
            if($this->delete($file, $result)){
                if($result->failed){
                    $deleted->failures[]=$result;
                }else{
                    $deleted->successes[]=$result;
                }
            }
        }
        $this->flash()->set("deleted_files",$deleted);
        return $this->redirectRoute("educ_action_ades_backup");
        }
    }
}
