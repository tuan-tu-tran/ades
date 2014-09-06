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

class ImportController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return "admin";
    }
    public function indexAction()
    {
        $this->flash()->get("errors");
        $this->params->missing_file = $this->flash()->get("missing_file");
        $this->params->upload_error = $this->flash()->get("upload_error");
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
            while(!$file->eof()){
                $line=$file->fgetcsv();
                ++$i;
                if ($line === array(NULL) ) {
                    continue;
                } elseif($header === NULL){
                    $header = $line;
                    $fieldCount = count($header);
                } elseif (count($line)!=$fieldCount){
                    $errors[]=array(
                        "type"=>"bad_count",
                        "lineNr"=>$i,
                        "count"=>count($line),
                        "expected"=>$fieldCount
                    );
                } else {
                    $csv[]=$line;
                }
            }
            error_log(var_export($header, TRUE));
            $this->flash()->set("errors", $errors);
            return $this->redirectRoute("educ_action_ades_import_proeco_preview");
        }
        return $this->redirectRoute("educ_action_ades_import_proeco");
    }

    public function previewAction()
    {
        $this->params->errors=$this->flash()->peek("errors");

        return $this->view("preview.html.twig");
    }
}
