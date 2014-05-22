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
use \FPDF;

class DetentionSlip
{
    private $errors=array();
    public $configSaved = FALSE;
    public $config_url="configurationbilletretenue.php";

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
                $this->errors[]="Impossible d'écrire le fichier de configuration: ".Tools::GetLastError();
            }
        }
        FlashBag::Set("result",$this);
        Tools::Redirect("configurationbilletretenue.php");
    }

    private function configFormAction()
    {
        if(!($result=FlashBag::Pop("result")) || !$result->errors) {
            $this->configSaved=$result;
        $configFile = self::ConfigFile();
        
        //read the config
        if (file_exists($configFile)) {
            $content=file_get_contents($configFile);
            if ($content===FALSE) {
                $this->errors[]="Impossible de lire le fichier de configuration: ".Tools::GetLastError();
                $config=self::GetEmptyConfig();
            } else {
                $config=unserialize($content);
                if($config===FALSE) {
                    $err="Le fichier de configuration contient des données invalides";
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
                $this->errors[]="Impossible d'écrire un fichier de configuration par default: ".Tools::GetLastError();
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
            "telecole" =>"Téléphone",
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

    private static function ConfigFile()
    {
        return Config::LocalFile("config_detention_slip.txt");
    }

    private static function WriteConfig($config)
    {
        $configFile = self::ConfigFile();
        $content=serialize($config);
        return $content && file_put_contents($configFile, $content);
    }

    const ERR_NO_CONFIG="noConfig";
    const ERR_READ_CONFIG="read";
    const ERR_CONFIG_CONTENT="configContent";
    const ERR_IMG_TYPE="imgType";
    public function previewAction()
    {
        $configFile = self::ConfigFile();
        if (!file_exists($configFile)) {
            $this->PreviewError(self::ERR_NO_CONFIG);
        } elseif ( ($content=file_get_contents($configFile)) === FALSE ) {
            $this->PreviewError(self::ERR_READ_CONFIG);
        } elseif ( ($config=unserialize($content)) === FALSE ) {
            $this->PreviewError(self::ERR_CONFIG_CONTENT);
        } elseif ( !($ext=Tools::GetImageType($config["imageenteteecole"])) ) {
            $this->PreviewError(self::ERR_IMG_TYPE);
        } else {
            if($config["typeimpression"] == "Paysage")
            {
                $pdf=new FPDF('L','mm','A5');
            }else{
                $pdf=new FPDF('P','mm','A4');
            }
            $pdf->AddPage();

            $pdf->Image($config["imageenteteecole"], 15, 10, 40,40, $ext);
            $pdf->SetFont('Arial','',14);
            $pdf->SetXY(90,10);
            $pdf->Cell(100,5,$config["nomecole"], 0, 2, 'C', 0);
            $pdf->SetXY(90,15);
            $pdf->Cell(100,5,$config["adresseecole"], 0, 2, 'C', 0);
            $pdf->SetXY(90,20);
            $pdf->Cell(100,5,"Téléphone:".$config["telecole"], 0, 2, 'C', 0);

            $pdf->SetFont('Arial','',12);
            $dt = date("d/m/y");
            $pdf->SetXY(140,35);
            $pdf->Cell(50,5,$config["lieuecole"].', le '.$dt, 0, 2, 'R');

            $pdf->SetFont('','B',24);
            $pdf->SetXY(70,45);
            $pdf->Cell(110,10, "INTITULE", 1, 0, 'C');

            $pdf->SetXY(10,65);
            $pdf->SetFont('', 'B',10);
            $chaine = "M. PRENOM NOM en classe de CLASSE\n";
            $pdf->Cell(200,5, $chaine, 0,0,'L');
            //$pdf->Write(5, $chaine);

            $pdf->SetXY(10,70);
            $pdf->SetFont('');
            $chaine = "a mérité une retenue de DUREE h ce DATE RETENUE à HEURE ";
            $chaine .= "(local LOCAL) pour le motif suivant\n";
            $pdf->Cell(200,5, $chaine, 0,0,'L');
            //$pdf->Write(5, $chaine);
            $pdf->SetXY(10,75);
            $pdf->SetFont('','B',12);
            $pdf->Write(5, "MOTIF");
            $pdf->SetFont('', 'B', 10);
            $pdf->SetXY(10,90);

            $chaine = "Matériel à apporter: JDC et matériel d'écriture - MATERIEL.\n";
            $chaine .= "Travail à effectuer: TRAVAIL.\n";
            $chaine .= "Veuillez prendre contact avec l'éducateur de votre enfant. Merci.\n";

            $pdf->Write(5, $chaine);

            $pdf->SetXY(10,110);
            $pdf->Cell(30,5,$config["signature1"], 0, 0, 'L', 0);
            $pdf->SetXY(80,110);
            $pdf->Cell(30,5,$config["signature2"], 0, 0, 'L', 0);
            $pdf->SetXY(150,110);
            $pdf->Cell(30,5,$config["signature3"], 0, 0, 'L', 0);

            $pdf->Output();
        }
    }

    private function PreviewError($error)
    {
        $this->Render("previewError.inc.php", array(
            "error"=>$error,
        ));
    }
}
