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
use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\FlashBag;

class DetentionSlip
{
    private $errors=array();
    public $configSaved = FALSE;

    public function parseRequest()
    {
        User::CheckIfLogged();
        User::CheckAccess(User::ACCESS_ADMIN);
        $action=Tools::GetDefault($_GET, "action");
        switch ($action) {
            default:
                if( Tools::IsPost()) {
                    $this->submitConfigFormAction();
                } else {
                    $this->configFormAction();
                }
                break;
        }
    }

    private function Render($template, $params=NULL)
    {
        View::Render("DetentionSlip/$template", $this, $params);
    }

    private function submitConfigFormAction()
    {
        $config=self::GetDefaultConfig();
        foreach ($config as $key=>$value) {
            if($key=="imageenteteecole") {
                continue;
            }
            if(!($config[$key] = Tools::GetDefault($_POST, $key)) && !$this->errors) {
                $this->errors[]="Veuillez remplir tous les champs";
            }
        }

        $this->submittedConfig=$config;

        if (!$this->errors) {
            if(!self::WriteConfig($config)) {
                $this->errors[]="Impossible d'�crire le fichier de configuration: ".Tools::GetLastError();
            }
        }
        FlashBag::Set("result",$this);
        Tools::Redirect("configurationbilletretenue.php");
    }

    private function configFormAction()
    {
        if(!($result=FlashBag::Pop("result")) || !$result->errors) {
            $this->configSaved=$result;
        $configFile = Config::LocalFile("config_detention_slip.ini");
        
        //read the config
        if (file_exists($configFile)) {
            $content=file_get_contents($configFile);
            if ($content===FALSE) {
                $this->errors[]="Impossible de lire le fichier de configuration: ".Tools::GetLastError();
                $config=self::GetEmptyConfig();
            } else {
                $config=unserialize($content);
                if($config===FALSE) {
                    $err="Le fichier de configuration contient des donn�es invalides";
                    $last_err=Tools::GetLastError();
                    if ($last_err) {
                        $err.=": $last_err";
                    } else {
                        $err.=".";
                    }
                    $this->errors[]=$err;
                    $config=self::GetEmptyConfig();
                }
            }
        } else {
            $config=self::GetDefaultConfig();
            if (!self::WriteConfig($config)) {
                $this->errors[]="Impossible d'�crire un fichier de configuration par default: ".Tools::GetLastError();
            }
        }
        } else {
            $config=$result->submittedConfig;
            $this->errors=$result->errors;
        }

        //display the config form
        $config["errors"]=$this->errors;
        $config["paysage"]=$config["typeimpression"]=="Paysage";

        $this->Render("configForm.inc.php", $config);
    }

    private static function GetDefaultConfig()
    {
        return array(
            "typeimpression" =>'Portrait',
            "imageenteteecole" =>'config/billetretenueimage.jpeg',
            "nomecole" =>"Ecole",
            "adresseecole" =>"Adresse",
            "telecole" =>"T�l�phone",
            "lieuecole" =>"Ville",
            "signature1" =>"signature1",
            "signature2" =>"signature2",
            "signature3" =>"signature3",
        );
    }

    private static function GetEmptyConfig()
    {
        $config=&self::GetDefaultConfig();
        foreach ($config as $key=>$value) {
            $config[$key]=NULL;
        }
        return $config;
    }

    private static function WriteConfig($config)
    {
        $configFile = Config::LocalFile("config_detention_slip.ini");
        $content=serialize($config);
        return $content && file_put_contents($configFile, $content);
    }
}
