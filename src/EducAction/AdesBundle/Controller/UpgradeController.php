<?php
/**
 * Copyright (c) 2015 Tuan-Tu TRAN
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

use EducAction\AdesBundle\Upgrade;
use EducAction\AdesBundle\Backup;

class UpgradeController extends Controller
{
    public function indexAction()
    {
        $result=$this->flash()->get("result");
        if($result){
            return $this->View("result.html.twig", $result);
        } else{
            if(!Upgrade::Required()){
                return $this->redirectRoute("educ_action_ades_homepage");
            } else {
                $versions=Upgrade::GetVersions();
                if($versions->fromBeforeTo){
                    $versions->restore=$this->flash()->get("restore");
                    return $this->View("index.html.twig", $versions);
                } else {
                    $versions->backup_files = Backup::getList();
                    return $this->View("restore.html.twig",$versions);
                }
            }
        }
    }

    public function upgradeAction()
    {
        if (Upgrade::execute($result)) {
            $this->flash()->set("result",$result);
        }
        return $this->redirectRoute("educ_action_ades_upgrade");
    }
}
