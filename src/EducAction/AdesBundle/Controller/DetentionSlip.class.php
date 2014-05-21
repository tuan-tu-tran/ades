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

class DetentionSlip
{
    private $errors=array();

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

    private function Render($template, $params=NULL)
    {
        View::Render("DetentionSlip/$template", $this, $params);
    }

    private function configFormAction()
    {
        $configFile = Config::LocalFile("config_detention_slip.ini");
        
        //read the config
        if (file_exists($configFile)) {
            $config=parse_ini_file($configFile);
        } else {
            $config=NULL;
        }

        if(!$config) {
            if ($config === FALSE) {
                $this->errors[]="Impossible de lire le fichier de configuration: ".Tools::GetLastError();
            }
            $config=self::GetDefaultConfig();
            if (!self::WriteConfig($config)) {
                $this->errors[]="Impossible d'écrire un fichier de configuration par default: ".Tools::GetLastError();
            }
        }

        //display the config form
        $config["errors"]=$this->errors;
        $config["paysage"]=$config["typeimpression"]=="paysage";

        $this->Render("configForm.inc.php", $config);
    }

    private static function GetDefaultConfig()
    {
        return array(
            "typeimpression" =>'Portrait',
            "imageenteteecole" =>'config/billetretenueimage.jpeg',
            "nomecole" =>"Ecole",
            "adresseecole" =>"Adresse",
            "telecole" =>"Téléphone",
            "lieuecole" =>"Ville",
            "signature1" =>"signature1",
            "signature2" =>"signature2",
            "signature3" =>"signature3",
        );
    }

    private static function WriteConfig($config)
    {
        $configFile = Config::LocalFile("config_detention_slip.ini");
        $content="";
        foreach ($config as $key=>$value) {
            $content.="$key=\"$value\"\n";
        }
        return file_put_contents($configFile, $content);
    }
}
