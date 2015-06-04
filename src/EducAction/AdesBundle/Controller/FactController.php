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
use EducAction\AdesBundle\Entities\Student;
use EducAction\AdesBundle\Entities\FactPrototype;

class FactController extends Controller implements IAccessControlled
{
    public function getRequiredPrivileges()
    {
        return array("admin","educ");
    }

    public function createAction($factTypeId, $studentId)
    {
        $student = Student::GetById($studentId) or $this->ThrowNotFoundException("Cet élève n'existe pas");
        $prototype = FactPrototype::GetById($factTypeId) or $this->ThrowNotFoundException("Ce type de fait n'existe pas");
        $params=new Bag();
        $params->student = $student;
        $params->prototype = $prototype;
        return $this->View("create.html.twig", $params);
    }
}

