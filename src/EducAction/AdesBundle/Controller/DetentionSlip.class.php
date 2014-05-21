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

use EducAction\AdesBundle\User;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\View;

class DetentionSlip
{
    public function parseRequest()
    {
        User::CheckIfLogged();
        User::CheckAccess(User::ACCESS_ADMIN);
        $action=Tools::GetDefault($_GET, "action");
        switch ($action) {
            default:
                $this->configFormAction();
                break;
        }
    }

    private function Render($template)
    {
        View::Render("DetentionSlip/$template", $this);
    }

    private function configFormAction()
    {
        //read the config
        //display the config form

        $this->Render("configForm.inc.php");
    }
}
