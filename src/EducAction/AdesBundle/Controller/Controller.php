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

use Symfony\Bundle\FrameworkBundle\Controller\Controller as SfController;
use Symfony\Component\HttpFoundation\Session\Session;

class Controller extends SfController {
    protected function View($template)
    {
        if(strpos($template,":") === FALSE){
            $controller=preg_replace("/^EducAction\\\\AdesBundle\\\\Controller\\\\(.+)Controller$/", "$1", get_class($this));
            $template="EducActionAdesBundle:$controller:$template";
        }
        $allParameters=array();
        $i=0;
        foreach (func_get_args() as $parameters) {
            if ($i>0 && $parameters) {
                if (!is_array($parameters)) {
                    $parameters=get_object_vars($parameters);
                }
                $allParameters=array_replace($allParameters, $parameters);
            }
            ++$i;
        }
        return $this->Render($template, $allParameters);
	}

    protected function GetSession()
    {
        $session=$this->getRequest()->getSession();
        if(!$session) {
            $session=new Session();
            $session->start();
        }
        return $session;
    }
}

