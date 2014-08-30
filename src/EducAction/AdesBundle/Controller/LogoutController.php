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
use EducAction\AdesBundle\Backup;
use \DateTime;
use \DateInterval;

class LogoutController extends Controller implements IProtected
{
    public function indexAction()
    {
        $params=$this->params;
        $lastBackup=NULL;
        $newBackup=NULL;
        $user=$this->getUser();
        if ($user->isAdmin())
        {
            //get last backup
            $lastBackup=Backup::getLast();
            $create=TRUE;
            if($lastBackup != NULL) {
                $time=$lastBackup->getTimestamp();
                $now=new DateTime();

                $params->last_backup_since = $now->diff($time);

                $maxAge = new DateInterval("PT12H");
                $limit = $now->sub($maxAge);
                $create = $time < $limit;
            }

            if($create) {
                //create a backup
                Backup::create("backup automatique", $this, $newBackup);
                $this->flash()->set("atLogout", true);
            }
        }
        $params->lastBackup = $lastBackup;
        $params->newBackup = $newBackup;
        $user->logout();
        return $this->View("index.html.twig");
    }
}
