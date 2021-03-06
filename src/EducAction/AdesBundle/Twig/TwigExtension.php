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

use EducAction\AdesBundle\Twig\TabPanelManager;
use EducAction\AdesBundle\Tools;

class TwigExtension extends \Twig_Extension
{
    private $requestStack;
    private $tabPanelManager;

    public function __construct($requestStack)
    {
        if(method_exists(get_parent_class(),"__construct")){
            parent::__construct();
        }
        $this->requestStack=$requestStack;
        $this->tabPanelManager=new TabPanelManager();
    }

    public function getName()
    {
        return "EducAction.AdesBundle.Twig.TwigExtension";
    }

    public function getFilters()
    {
        $filters=array();
        $filters[]=new \Twig_SimpleFilter("file_size", array($this, "formatFileSize"));
        $filters[]=new \Twig_SimpleFilter("weekday", array($this, "getWeekDay"));
        $filters[]=new \Twig_SimpleFilter("overlib", array($this, "overlib"), array("is_safe"=>array("html")));
        return $filters;
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
        $functions[]=new \Twig_SimpleFunction("tabstrip", array($this->tabPanelManager,"strip"),
            array(
                "is_safe"=>array("html"),
                "needs_environment"=>TRUE
            )
        );
        $functions[]=new \Twig_SimpleFunction("tab", array($this->tabPanelManager,"tab"),
            array(
                "is_safe"=>array("html"),
            )
        );
        $functions[]=new \Twig_SimpleFunction("endtabs", array($this->tabPanelManager,"end"),
            array(
                "is_safe"=>array("html"),
            )
        );
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
            if($identification = Tools::GetDefault($_SESSION,"identification")) {
                $who = $identification['nom'];
                $who.=" ";
                $who.= $identification['prenom'];
                $params["who"]=$who;
            }
            return $env->render("EducActionAdesBundle::whosthere.html.twig", $params);
        }
    }

    const GB=1073741824; //1024*1024*1024
    const MB=1048576; //1024*1024
    const KB=1024;
    public function formatFileSize($size,$dec=0,$byte="o")
    {
        if($size>=self::GB){
            return sprintf("%.".$dec."f G%s", $size/self::GB, $byte);
        }else if($size>=self::MB){
            return sprintf("%.".$dec."f M%s", $size/self::MB, $byte);
        }else if($size>=self::KB){
            return sprintf("%.".$dec."f K%s", $size/self::KB, $byte);
        }else{
            return sprintf("%d %s", $size, $byte);
        }
	}

    /**
     * Return the weekday of a date in french
     *
     * @param string $date a string that can be parsed by strtotime.
     * @return string the week day of the date in french
     */
    private static $weekdays=["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"];
    public function getWeekDay($date)
    {
        return self::$weekdays[date("w", strtotime($date))];
    }
}
