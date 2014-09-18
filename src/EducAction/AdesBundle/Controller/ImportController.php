<?php
/**
 * Copyright (c) 2014 Tuan-Tu TRAN
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

use \SplFileObject;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\Db;

class ImportController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return "admin";
    }
    public function indexAction()
    {
        $this->params->missing_file = $this->flash()->get("missing_file");
        $this->params->upload_error = $this->flash()->get("upload_error");
        $this->flash()->clear();
        return $this->View("index.html.twig");
    }

    public function uploadAction()
    {
        $request=$this->getRequest();
        $file=$request->files->get("csv");
        if($file==NULL){
            $this->flash()->set("missing_file", TRUE);
        } else if (!$file->isValid()) {
            $this->flash()->set("upload_error", $file->getError());
        } else {
            $file=$file->openFile();
            $file->setFlags(SplFileObject::DROP_NEW_LINE);
            $header=NULL;
            $data=array();
            $i=0;
            $errors=array();
            $students=array();
            $studentByUniqueId=array();
            while(!$file->eof()){
                $line=$file->fgetcsv();
                ++$i;
                if ($line === array(NULL) ) {
                    continue;
                } elseif($header === NULL){
                    $header = Tools::map("utf8_encode",$line);
                    $fieldCount = count($header);
                    $checkHeader = function($h) use (&$errors, $header, $i) {
                        if(!in_array($h, $header)) {
                            $errors[]=array(
                                "type"=>"missing_header",
                                "header"=>$h,
                                "lineNr"=>$i
                            );
                        }
                    };
                    $checkHeader("Nom Elève");
                    $checkHeader("Prénom Elève");
                    $checkHeader("AnFF");
                    $checkHeader("Classe");
                    //$checkHeader("DateAnniv");
                    //$checkHeader("Matric Info");
                    //$checkHeader("NomPrénom Resp");
                    //$checkHeader("EMail Responsable");
                    //$checkHeader("Tél Responsable");
                    //$checkHeader("GSM Responsable");
                    //$checkHeader("Tél Rem Responsable");
                    if($errors){
                        break;
                    }
                    $indexByHeader=array();
                    foreach($header as $index => $field){
                        if(!isset($indexByHeader[$field])){
                            $indexByHeader[$field] = $index;
                        } else {
                            $errors[]=array(
                                "type"=>"duplicate_field",
                                "header"=>$field,
                                "lineNr" => $i
                            );
                        }
                    }
                    if($errors){
                        break;
                    }
                } elseif (count($line)!=$fieldCount){
                    $errors[]=array(
                        "type"=>"bad_count",
                        "lineNr"=>$i,
                        "count"=>count($line),
                        "expected"=>$fieldCount
                    );
                } else {
                    $get=function($h , $mandatory=TRUE) use ($header, $line, $indexByHeader) {
                        if(!isset($indexByHeader[$h]) && $mandatory){
                            throw new \Exception("field $h not in ".var_export($header, TRUE));
                        }
                        $value="";
                        if(Tools::TryGet($indexByHeader, $h, $index)){
                            $value=$line[$indexByHeader[$h]];
                        }
                        return utf8_encode($value);
                    };
                    $s=array();
                    $s["nom"]=$get("Nom Elève");
                    $s["prenom"]=$get("Prénom Elève");
                    $s["classe"]=$get("AnFF").$get("Classe");
                    $bday=$get("DateAnniv", FALSE);
                    if($bday){
                        if(!preg_match("/^(\\d\\d\\/){2}.{4}$/", $bday)){
                            $errors[]=array(
                                "type"=>"bad_birthday",
                                "lineNr"=>$i
                            );
                        }else{
                            $bday=substr($bday, 0, 5);
                        }
                    }
                    $s["anniv"]=$bday;
                    $s["codeInfo"]=$get("Matric Info", FALSE);
                    $s["nomResp"] = $get("NomPrénom Resp", FALSE);
                    $s["courriel"] = $get("EMail Responsable", FALSE);
                    $s["telephone1"] = $get("Tél Responsable", FALSE);
                    $s["telephone2"] = $get("GSM Responsable", FALSE);
                    $s["telephone3"] = $get("Tél Rem Responsable", FALSE);
                    $idunique = $s["nom"].$s["prenom"].$s["classe"].$s["anniv"].$s["codeInfo"];
                    $s["idunique"]=$idunique;
                    $s["lineNr"] = $i;
                    if(!isset($studentByUniqueId[$idunique])){
                        $studentByUniqueId[$idunique]=array(
                            "student"=>$s
                        );
                    } else {
                        $record=&$studentByUniqueId[$idunique];
                        if(!isset($record["error"])){
                            $e=array(
                                "type"=>"duplicate_record",
                                "student"=>$s,
                                "lines"=>array($record["student"]["lineNr"])
                            );
                            $record["error"]=$e;
                            $errors[]=&$record["error"];
                        }
                        $record["error"]["lines"][]=$i;
                    }
                    $students[]=$s;
                }
            }
            if(!$errors && count($students) == 0) {
                $errors[]=array(
                    "type"=>"no_student"
                );
            }
            $this->flash()->set("errors", $errors);
            $this->flash()->set("students", $students);
            return $this->redirectRoute("educ_action_ades_import_proeco_preview");
        }
        return $this->redirectRoute("educ_action_ades_import_proeco");
    }

    public function previewAction()
    {
        $this->params->errors=$this->flash()->peek("errors");
        $this->params->students=$this->flash()->peek("students");

        return $this->view("preview.html.twig");
    }

    public function confirmAction()
    {
        $students=$this->flash()->peek("students");
        $errors = $this->flash()->get("errors");
        if($errors || !$students)
        {
            return $this->redirectRoute("educ_action_ades_import_proeco");
        }
        else
        {
            $fields=array(
                "dermodif",
                "nom","prenom",
                "classe","anniv","codeInfo",
                "nomResp", "courriel","telephone1",
                "telephone2","telephone3","idunique"
            );
            $updateFields=array_slice($fields, 0, -1);
            $updateClause=Tools::map(function($f){
                return "$f=?";
            },$updateFields);
            $query="INSERT INTO ades_eleves(".implode($fields,",").")";
            $query.="VALUES".Db::getWhereInClause(count($fields));
            $query.="ON DUPLICATE KEY UPDATE ";
            $query.=implode($updateClause,",");
            error_log($query);
            $db=Db::GetInstance();
            $now=new \Datetime();
            foreach($students as $s)
            {
                $params=array();
                $s["dermodif"]=$now;
                foreach($fields as $f){
                    $params[]=$s[$f];
                }
                foreach($updateFields as $f){
                    $params[]=$s[$f];
                }

                foreach($params as &$p){
                    if(is_string($p)) {
                        $p=utf8_decode($p);
                    }
                }
                $db->execute($query, $params);
            }
            return $this->redirectRoute("educ_action_ades_import_proeco_done");
        }

    }

    public function doneAction()
    {
        if(!$students=$this->flash()->get("students")) {
            return $this->redirectRoute("educ_action_ades_import_proeco");
        } else {
            $this->params->students=$students;
            return $this->View("done.html.twig");
        }
    }
}
