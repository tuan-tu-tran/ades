<?php
/**
 * Copyright (c) 2014 EducAction
 * Copyright (c) 2015 Tuan-Tu Tran : rework of the classfaits.php and fait.php?mode=nouveau
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

use EducAction\AdesBundle\Bag;
use EducAction\AdesBundle\User;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Entities\Student;
use EducAction\AdesBundle\Entities\FactPrototype;
use EducAction\AdesBundle\Entities\Fact;
use EducAction\AdesBundle\Entities\Detention;

class FactController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return array("admin","educ");
    }

    public function editAction($id)
    {
        $fact=Fact::GetById($id) or $this->throwNotFoundException("Ce fait n'existe pas");
        $student=$fact->getStudent();
        $prototype=FactPrototype::GetByIdForForm($fact->prototypeId);
        $params=$this->getFormParams($student, $fact, $prototype, TRUE);
        return $this->View("create.html.twig", $params);
    }
    public function createAction($factTypeId, $studentId)
    {
        $student = Student::GetById($studentId) or $this->ThrowNotFoundException("Cet élève n'existe pas");
        $prototype = FactPrototype::GetByIdForForm($factTypeId) or $this->ThrowNotFoundException("Ce type de fait n'existe pas");
        $fact=Fact::GetNew($factTypeId, $studentId, User::GetId());
        $params=$this->getFormParams($student, $fact, $prototype, FALSE);
        return $this->View("create.html.twig", $params);
    }

    public function addStudentToDetentionAction($detentionId)
    {
        $detention = Detention::GetById($detentionId) or $this->ThrowNotFoundException("Cette date de retenue n'existe pas");
        $factTypeId = FactPrototype::GetIdByDetentionTypeId($detention->typeId);
        if($factTypeId < 0){
            throw new \Exception("could not get prototype id for dentention $dentention with type ".$dentention->typeId);
        }
        $prototype = FactPrototype::GetByIdForForm($factTypeId) or $this->throwException("No prototype for id $factTypeId yielded from detention $detentionId (type: ".$detention->typeId.")");
        $params=$this->getFormParams(NULL, NULL, $prototype, FALSE);
        return $this->View("create.html.twig", $params);
    }

    private function getFormParams($student, $fact, $prototype, $editing)
    {
        $params=new Bag();
        $params->student = $student;
        $params->prototype = $prototype;
        $params->editing=$editing;
        $hasDetentionDate= FALSE;
        foreach($prototype->fields as $f){
            $f->value=$fact?$fact->getValue($f):NULL;
            if($f->isDetentionDate){
                if($hasDetentionDate){
                    throw new \Exception("prototype ".$prototype->id." has multiple detention dates");
                }
                $hasDetentionDate=TRUE;
                $f->detentions=Detention::getVisibleDates($prototype->detentionType);
                if(count($f->detentions)==0){
                    $params->no_dates=TRUE;
                } else if($f->value<=0){
                    $notfull=FALSE;
                    foreach($f->detentions as $d){
                        if($d->freePlaces > 0){
                            $notfull=TRUE;
                        }
                    }
                    if(!$notfull){
                        $params->no_dates=TRUE;
                    }
                }
            }
        }
        $allStudents = Student::GetAll();
        if($student){
        foreach($allStudents as $i=>$s){
            if($s->id == $student->id){
                unset($allStudents[$i]);
            }
        }
        }
        usort($allStudents, Tools::CompareBy("class","lastName","firstName"));
        $params->allStudents = $allStudents;

        return $params;
    }

    public function postAction()
    {
        $request=$this->get("request");
        $post=$request->request;
        $type = $post->get("type");
        $id=$post->get("idfait", 0);
        $db=Db::GetInstance();
        if($id){
            $db->execute("UPDATE ades_faits SET supprime='O' WHERE idfait=?", $id);
        }
        $prototype=FactPrototype::GetByIdForForm($post->get("type")) or $this->throwNotFoundException("post: type $type");
        $fields=array();
        $values=array();
        $markers=array();
        $all=$post->all();
        $extraStudentIds=$post->get("extraStudentIds");
        $indexIdOrigine=-1;
        $indexIdStudent=-1;
        foreach($prototype->fields as $f) {
            $name = $f->name;
            if($name!="idfait"){
                $fields[]="`$name`";
                if($name=="qui"){
                    $v=User::GetId();
                }elseif($name=="idorigine"){
                    $v=$id;
                    $indexIdOrigine=count($values);
                } else {
                    Tools::TryGet($all, $name, $v) or $this->throwNotFoundException("post: $name");
                    if($f->isDate){
                        $v=\DateTime::createFromFormat("j/n/Y", $v);
                    }
                    if($name=="ideleve"){
                        $indexIdStudent = count($values);
                    }
                }
                $values[]=$v;
                $markers[]="?";
            }
        }

        if($extraStudentIds && ($indexIdOrigine<0 || $indexIdStudent <0)){
            throw new \Exception("could not find index for idorigine $indexIdOrigine or ideleve $indexIdStudent to handle mulitple facts: ".var_export($post->all(), TRUE));
        }
        $query="INSERT INTO ades_faits("
            .join(",",$fields)
            ." ,`dermodif`"
            ." ) VALUES ( ".join(",", $markers).",?)";
        $values[]=new \DateTime();
        $db->execute($query, $values);

        if($extraStudentIds){
            $values[$indexIdOrigine]=0;
            foreach($extraStudentIds as $id){
                $values[$indexIdStudent]=$id;
                $db->execute($query, $values);
            }
        }

        $db->execute("
            UPDATE ades_retenues
            JOIN (
                SELECT r.idretenue AS tmp_id_retenue, SUM(f.idfait IS NOT null) AS real_occ
                FROM ades_retenues r
                LEFT join ades_faits f ON r.idretenue = f.idretenue AND f.supprime = 'N'
                GROUP BY r.idretenue
            ) tmp ON tmp_id_retenue = idretenue
            SET occupation = real_occ
            WHERE occupation != real_occ
        ");

        $this->flash()->set("studentId", $post->get("ideleve"));

        return $this->redirectRoute("educ_action_ades_fact_done");
    }

    public function showDoneAction()
    {
        $studentId = $this->flash()->get("studentId") or $this->throwNotFoundException("Missing student");
        return $this->View("done.html.twig", array("studentId"=>$studentId));
    }
}

