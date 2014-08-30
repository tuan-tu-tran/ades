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

namespace EducAction\AdesBundle\KernelListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use EducAction\AdesBundle\Controller\AccessController;
use EducAction\AdesBundle\User;

class KernelListener
{
    private $router;
    public function __construct($router)
    {
        $this->router=$router;
    }

    public function onControllerListener(FilterControllerEvent $event)
    {
        $request=$event->getRequest();
        $controller=$event->getController();
        if(!is_array($controller)){
            throw new \Exception("controller must be an array, not a ".get_class($controller)." (_route: ".$request->get("_route").")");
        }
        $instance=$controller[0];
        $action = $controller[1];
        if(is_a($instance, "EducAction\\AdesBundle\\Controller\\IProtected")){
            $allowed=$instance->isPublicAction($action);
            if(!$allowed){
            $session = $request->getSession();
            if(!$session->isStarted()) {
                $session->start();
            }
            if(!User::IsLogged()) {
                $event->setController(array(new AccessController($request, $this->router), "redirectLogin"));
            } elseif (is_a($instance, "EducAction\\AdesBundle\\Controller\\IAccessControlled")) {
                $requiredPrivileges=$instance->getRequiredPrivileges();
                $userPrivilege = $_SESSION["identification"]["privilege"];
                if(!in_array($userPrivilege, $requiredPrivileges)){
                    $event->setController(array(new AccessController($request), "unauthorized"));
                }
            }
            }
        }
    }
}

