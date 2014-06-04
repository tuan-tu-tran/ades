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

namespace EducAction\AdesBundle\Twig;

use EducAction\AdesBundle\Config;

class TwigExtension extends \Twig_Extension
{
    private $requestStack;

    public function __construct($requestStack)
    {
        if(method_exists(get_parent_class(),"__construct")){
            parent::__construct();
        }
        $this->requestStack=$requestStack;
    }

    public function getName()
    {
        return "EducAction.AdesBundle.Twig.TwigExtension";
    }

    public function getFunctions()
    {
        $functions=array();
        $functions[]=new \Twig_SimpleFunction("overlib", array($this, "overlib"), array("is_safe"=>array("html")));
        $functions[]=new \Twig_SimpleFunction("school", array("EducAction\\AdesBundle\\Config", "getSchoolConfig"));
        $functions[]=new \Twig_SimpleFunction("whos_there", array($this, "whos_there"),
            array(
                "is_safe"=>array("html"),
                "needs_environment"=>TRUE
            )
        );
        $functions[]=new \Twig_SimpleFunction("user_is_logged", array("EducAction\\AdesBundle\\User","IsLogged"));
        $functions[]=new \Twig_SimpleFunction("user_has_access", array("EducAction\\AdesBundle\\User","HasAccess"));
        $functions[]=new \Twig_SimpleFunction("unread_mail", array("EducAction\\AdesBundle\\MiniMail","UnreadMailCount"));
        return $functions;
    }

    public function overlib($text)
    {
        $hint=htmlspecialchars(json_encode($text));
		return " onmouseover=\"return overlib($hint);\" onmouseout=\"return nd();\"";
    }

    public function whos_there($env)
    {
        $request=$this->requestStack->getCurrentRequest();
        if($request) {
            $params=array();
            $params["ip"] = $_SERVER['REMOTE_ADDR'];
            $params["hostname"] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            if($identification = $request->getSession()->get("identification")) {
                $who = $identification['nom'];
                $who.=" ";
                $who = $identification['prenom'];
                $params["who"]=$who;
            }
            return $env->render("EducActionAdesBundle::whosthere.html.twig", $params);
        }
    }
}
