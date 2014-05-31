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
use EducAction\AdesBundle\ViewParameters;
use EducAction\AdesBundle\FlashBagWrapper;

class Controller extends SfController
{
    protected $params;
    private $flashWrapper = NULL;

    public function __construct()
    {
        if(method_exists(get_parent_class(),"__construct")){
            parent::__construct();
        }
        $this->params=new ViewParameters();
    }

    protected function View($template)
    {
        if(strpos($template,":") === FALSE){
            $controller=preg_replace("/^EducAction\\\\AdesBundle\\\\Controller\\\\(.+)Controller$/", "$1", get_class($this));
            $template="EducActionAdesBundle:$controller:$template";
        }
        $allParameters=array();
        $givenParameters=array_merge(array($this->params), array_slice(func_get_args(), 1));
        $i=0;
        foreach ($givenParameters as $parameters) {
            if ($parameters) {
                if (!is_array($parameters)) {
                    if(is_a($parameters, "EducAction\\AdesBundle\\ViewParameters")){
                        $parameters=get_object_vars($parameters);
                    } else {
                        throw new \Exception("View parameter cannot be a ".get_class($parameters));
                    }
                }
                $allParameters=array_replace($allParameters, $parameters);
            }
            ++$i;
        }
        return $this->Render($template, $allParameters);
	}

    protected function flash()
    {
        if(!$this->flashWrapper) {
            $this->flashWrapper = new FlashBagWrapper($this->getRequest()->getSession()->getFlashBag());
        }
        return $this->flashWrapper;
    }
}

