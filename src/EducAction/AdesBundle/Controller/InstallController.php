<?php
/**
 * Copyright (c) 2014 Educ-Action
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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Path;
use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\Tools;
use EducAction\AdesBundle\View;
use EducAction\AdesBundle\FlashBag;

class InstallController extends Controller {
	const ACTION_CONFIG_DB="configure_db";
	const ACTION_CREATE_TABLES="create_tables";
	const ACTION_CONFIG_SCHOOL="configure_school";

	public function parseRequest(){
		//get the action
		$action = Tools::GetDefault($_GET,"action");

		switch($action){
			case self::ACTION_CONFIG_DB:
                if (Tools::IsPost()) {
                    $this->submitDbConfigAction();
                } else {
                    $this->configureDbAction();
                }
				break;

			case self::ACTION_CREATE_TABLES:
                $this->createTablesAction();
				break;

			case self::ACTION_CONFIG_SCHOOL:
                if(Tools::IsPost()) {
                    $this->submitSchoolConfigAction();
                } else {
                    $this->configureSchoolAction();
                }
				break;
			default:
                $this->indexAction();
                break;
		}
	}

    private function _Render($view, $params=NULL)
    {
        if ($params == NULL) {
            $params=$this;
        }
        View::Render("Install/$view", array("install"=>$params));
        exit;
    }

    private function indexAction()
    {
        $this->Render("index.inc.php");
    }

    private function configureDbAction()
    {
        $configure_db_result=FlashBag::Get("configure_db_result");
        if(file_exists(Config::DbConfigFile()) && !$configure_db_result) {
            $this->Render("overwrite_forbidden.inc.php");
        } elseif (!$configure_db_result) {
            //show config form
            $this->host=NULL;
            $this->username=NULL;
            $this->pwd=NULL;
            $this->dbname=NULL;
            $this->Render("db_config_form.inc.php");
        } elseif (!$configure_db_result->valid_config) {
            $this->Render("db_config_form.inc.php", $configure_db_result);
        } else {
            FlashBag::Clear();
            $this->Render("db_config_written.inc.php", $configure_db_result);
        }
    }

    private function submitDbConfigAction()
    {
        if(file_exists(Config::DbConfigFile())) {
            $this->Render("overwrite_forbidden.inc.php");
        } else if(!$this->ConfigIsValid() || $this->WriteDbConfig()){
            FlashBag::Set("configure_db_result", $this);
            $this->Redirect(self::ACTION_CONFIG_DB);
        } else {
            $this->ShowWriteError(Config::DbConfigFile(), $this->GetDbConfigSubmitUrl());
        }
    }

    private function createTablesAction()
    {
        $create_tables_result=FlashBag::Pop("create_tables_result");
        if (Tools::IsPost() || (!$create_tables_result && !$this->GetTables()) ) {
            $this->created=$this->CreateTables();
            FlashBag::Set("create_tables_result", $this);
            $this->Redirect(self::ACTION_CREATE_TABLES);
        } elseif ($create_tables_result) {
            if($create_tables_result->created) {
                $this->Render("tables_created.inc.php", $create_tables_result);
            }else{
                $this->Render("tables_creation_failed.inc.php", $create_tables_result);
            }
        } else {
            //test if there already are tables in the db:
            $this->Render("create_tables.inc.php");
        }
    }

    private function _Redirect($action)
    {
        Tools::Redirect("creation.php?action=$action");
    }

    private function GetTables()
    {
        $result=Db::GetInstance()->query("SHOW TABLES");
        $this->tables=array();
        foreach ($result as $row) {
            $this->tables[] = $row[0];
        }
        return $this->tables;
    }

    private function configureSchoolAction()
    {
        $configure_school_result=FlashBag::Get("configure_school_result");
        if(file_exists(Config::SchoolConfigFile()) && !$configure_school_result) {
            $this->Render("overwrite_school_forbidden.inc.php");
        } elseif (!$configure_school_result) {
            $this->schoolname = NULL;
            $this->title = NULL;
            $this->Render("school_config_form.inc.php");
        } elseif (!$configure_school_result->valid_config) {
            $this->Render("school_config_form.inc.php", $configure_school_result);
        } else {
            FlashBag::Clear();
            $this->Render("school_config_written.inc.php", $configure_school_result);
        }
    }

    private function submitSchoolConfigAction()
    {
        if(file_exists(Config::SchoolConfigFile())) {
            $this->Render("overwrite_school_forbidden.inc.php");
        } else if(!$this->SchoolConfigIsValid() || $this->WriteSchoolConfig()) {
            FlashBag::Set("configure_school_result", $this);
            $this->Redirect(self::ACTION_CONFIG_SCHOOL);
        } else {
            $this->ShowWriteError(Config::SchoolConfigFile(), $this->GetSchoolConfigSubmitUrl());
        }
    }

	private function SchoolConfigIsValid(){
		$this->schoolname = $_POST["schoolname"];
		$this->title = $_POST["title"];
        if($this->schoolname!=NULL && $this->title!=NULL) {
            $this->valid_config= TRUE;
        } else {
            $this->missing_fields = TRUE;
            $this->valid_config= FALSE;
        }
        return $this->valid_config;
	}

	private function WriteSchoolConfig(){
		$format=<<<EOF
<?php
define("ECOLE",%s);
define("TITRE",%s);

EOF;
		$file=fopen(Config::SchoolConfigFile(),"wt");
		if($file){
			fprintf($file, $format
				, var_export($this->schoolname, true)
				, var_export($this->title, true)
			);
			fclose($file);
			return true;
		}else{
			return false;
		}
	}
	private function ConfigIsValid(){
		$this->host=$_POST["sqlserver"];
		$this->username=$_POST["utilisateursql"];
		$this->pwd=$_POST["motdepassesql"];
		$this->dbname=$_POST["nomdelabasesql"];
		if(
			$this->host!=NULL
			&& $this->username!=NULL
			&& $this->pwd!=NULL
			&& $this->dbname!=NULL
		){
			$valid=Db::GetInstance($this->host, $this->username, $this->pwd, $this->dbname)->connect();
			if(!$valid) $this->error=Db::GetInstance()->error();
		}else{
			$valid=false;
			$this->missing_fields=true;
		}
        $this->valid_config=$valid;
		return $valid;
	}

	private function CreateTables(){
		$path=DIRNAME(__FILE__)."/../Resources/sql_scripts/creation.sql";
		$commandes = file($path);
		$uneCommande = "";
		$error=false;
		foreach ($commandes as $uneLigne){
			// supprimer les commentaires dans le fichier .sql
			if (substr($uneCommande, 0, 2) == "--")
				$uneCommande = "";
			$uneCommande .= trim($uneLigne);
			$longueur = strlen($uneCommande);
			$dernier = substr($uneCommande, $longueur-1, 1);
			if ($dernier == ";"){
				if(!Db::GetInstance()->TryExecute($uneCommande)){
					$this->error_command=$uneCommande;
					$this->error=Db::GetInstance()->error();
					$error=true;
					break;
				}
				$uneCommande = "";
			}
		}
		if(!$error && Upgrade::Required()){
			$upgrade=new Upgrade();
			if(!$upgrade->UpgradeDb()){
				if(!$upgrade->fromBeforeTo){
					throw new Exception("Upgrade during install failed because trying to upgrade from ".$upgrade->fromVersion." to ".$upgrade->toVersion);
				}
				$this->failedScript=$upgrade->failedScript;
				$this->error=$upgrade->failedScriptError;
				$error=true;
			}
		}
		return !$error;
	}

	function WriteDbConfig(){
		// Rami Adrien création du fichier confdb.inc.php
		$fichierconfdb = fopen(Config::DbConfigFile(),"wt");
		if(!$fichierconfdb){
			return false;
		}else{
			$format=<<<EOF
<?php
// SERVEUR SQL
\$sql_serveur=%s;
// LOGIN SQL
\$sql_user=%s;
// MOT DE PASSE SQL
\$sql_passwd=%s;
// NOM DE LA BASE DE DONNEES
\$sql_bdd=%s;

\$sql_prefix="";

EOF;
			fprintf($fichierconfdb, $format
				, var_export($_POST["sqlserver"], true)
				, var_export($_POST["utilisateursql"], true)
				, var_export($_POST["motdepassesql"], true)
				, var_export($_POST["nomdelabasesql"], true)
			);
			fclose($fichierconfdb);
			return true;
		}
	}

	private function ShowWriteError($fname, $resubmitAction)
    {
		$this->error=error_get_last()["message"];
		$this->system_user=posix_getpwuid(posix_geteuid())["name"];
		$this->config_filename=realpath(DIRNAME($fname)).DIRECTORY_SEPARATOR.basename($fname);
		$this->resubmitAction=$resubmitAction;
        $this->Render("write_error.inc.php");
	}

	private function GetLink($action, $text){ echo "<a href='".$this->GetUrl($action)."'>".$text."</a>"; }
	private function GetUrl($action){ return "creation.php?action=".$action; }
	public function GetDbConfigLink($text){ $this->GetLink(self::ACTION_CONFIG_DB, $text);}
	public function GetCreateTableLink($text){ $this->GetLink(self::ACTION_CREATE_TABLES, $text);}
	public function GetSchoolConfigLink($text){ $this->GetLink(self::ACTION_CONFIG_SCHOOL, $text);} 
	public function GetDbConfigSubmitUrl(){return $this->GetUrl(self::ACTION_CONFIG_DB);}
	public function GetSchoolConfigSubmitUrl(){return $this->GetUrl(self::ACTION_CONFIG_SCHOOL);}
	public function CanConfigureSchool(){ return !file_exists(Config::SchoolConfigFile()); }

	public static function CheckIfNeeded()
	{
		if(!file_exists(Config::DbConfigFile()) || !file_exists(Config::SchoolConfigFile())){
			Tools::Redirect("creation.php");
		}
	}
}
