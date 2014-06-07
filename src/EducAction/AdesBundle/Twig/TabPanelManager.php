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

use EducAction\AdesBundle\Tools;

class TabPanel
{
    private $twig;
    private $firstTab;
    private $selectedTab;
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig=$twig;
        $this->firstTab=TRUE;
    }

    public function strip($headers, $options=array())
    {
        $this->selectedTab = Tools::GetDefault($options, "selected");
        foreach($headers as $key=>&$value){
            if(is_string($value)) {
                $value=array("text"=>$value);
            }
            if(!$key || !isset($value["text"])) {
                throw new \Exception("tab key or text cannot be null");
            }
            if(Tools::GetDefault($value,"selected")){
                $this->selectedTab=$key;
            } else {
                $value["selected"] = $this->selectedTab == $key;
            }
        }
        return $this->twig->render("EducActionAdesBundle:TabPanel:strip.html.twig", array("tabs"=>$headers, "options"=>$options));
    }

    public function tab($id)
    {
        if(!$this->firstTab){
            echo "</div>";
        }
        $style=($id==$this->selectedTab)?"":"display:none";
        echo "<div class='tab-panel-panel' style='$style' id='$id'>";
        $this->firstTab=FALSE;
    }

    public function end()
    {
        if(!$this->firstTab){
            echo "</div>";
        }
        echo "</div></div>";
    }
}

class TabPanelManager
{
    private $stack;
    private $current=NULL;
    public function __construct()
    {
        $this->stack=array();
    }

    public function strip(\Twig_Environment $twig /*, ...*/)
    {
        if($this->current!=NULL) {
            array_push($this->stack, $this->current);
        }
        $this->current=new TabPanel($twig);
        return call_user_func_array(array($this->current, "strip"), array_slice(func_get_args(),1));
    }

    public function tab()
    {
        if($this->current == NULL) {
            throw new \Exception("no current tab panel");
        }
        return call_user_func_array(array($this->current, "tab"), func_get_args());
    }

    public function end()
    {
        if($this->current == NULL) {
            throw new \Exception("no current tab panel");
        }
        return call_user_func_array(array($this->current, "end"), func_get_args());
    }
}

