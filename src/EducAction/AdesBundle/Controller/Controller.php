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
use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\MiniMail;
use EducAction\AdesBundle\User;

class Controller extends SfController
{
    protected $params;
    private $flashWrapper = NULL;
    private $baseConfig = NULL;

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
        if ($this->baseConfig === NULL) {
            if (file_exists(Config::SchoolConfigFile()))
            {
                require Config::SchoolConfigFile();
                $this->baseConfig=array(
                    "ECOLE"=>ECOLE,
                    "TITRE"=>TITRE
                );
            } else {
                $this->baseConfig=array();
            }
            $this->baseConfig["ip"] = $_SERVER['REMOTE_ADDR'];
            $this->baseConfig["hostname"] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            if($identification = $this->getRequest()->getSession()->get("identification")) {
                $who = $_SESSION['identification']['nom'];
                $who.=" ";
                $who = $_SESSION['identification']['prenom'];
                $this->baseConfig["who"]=$who;
            }
            $this->baseConfig["user"]=new User;
        }


        $allParameters=array();
        $givenParameters=array(
            array(
                "base"=>$this->baseConfig
            ),
            $this->params
        );
        $givenParameters=array_merge($givenParameters, array_slice(func_get_args(), 1));
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

