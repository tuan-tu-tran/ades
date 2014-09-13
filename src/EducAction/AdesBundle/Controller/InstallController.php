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

use EducAction\AdesBundle\Db;
use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\Tools;

class InstallController extends Controller
{
    public function indexAction()
    {
        return $this->View("index.html.twig");
    }

    public function configureDbAction()
    {
        $configure_db_result=$this->flash()->peek("configure_db_result");
        if(file_exists(Config::DbConfigFile()) && !$configure_db_result) {
            $this->checkCanConfigureSchool();
            return $this->View("overwrite_forbidden.html.twig");
        } elseif (!$configure_db_result) {
            //show config form
            $this->params->host=NULL;
            $this->params->username=NULL;
            $this->params->pwd=NULL;
            $this->params->dbname=NULL;
            return $this->View("db_config_form.html.twig");
        } elseif (!$configure_db_result->valid_config) {
            return $this->View("db_config_form.html.twig", $configure_db_result);
        } else {
            $this->flash()->clear();
            return $this->View("db_config_written.html.twig", $configure_db_result);
        }
    }

    public function submitDbConfigAction()
    {
        if(file_exists(Config::DbConfigFile())) {
            return $this->redirect($this->generateUrl("educ_action_ades_install_db"));
        } else if(!$this->ConfigIsValid() || $this->WriteDbConfig()){
            $this->flash()->set("configure_db_result", $this->params);
            return $this->redirect($this->generateUrl("educ_action_ades_install_db"));
        } else {
            return $this->ShowWriteError(Config::DbConfigFile(), $this->generateUrl("educ_action_ades_install_db_submit"));
        }
    }

    public function createTablesAction()
    {
        $create_tables_result=$this->flash()->peek("create_tables_result");
        if (Tools::IsPost() || (!$create_tables_result && !$this->GetTables()) ) {
            $this->params->created=$this->CreateTables();
            $this->flash()->set("create_tables_result", $this->params);
            return $this->redirect($this->generateUrl("educ_action_ades_install_tables"));
        } elseif ($create_tables_result) {
            $this->checkCanConfigureSchool();
            if($create_tables_result->created) {
                return $this->View("tables_created.html.twig", $create_tables_result);
            }else{
                return $this->View("tables_creation_failed.html.twig", $create_tables_result);
            }
        } else {
            //test if there already are tables in the db:
            $this->checkCanConfigureSchool();
            return $this->View("create_tables.html.twig");
        }
    }

    private function checkCanConfigureSchool()
    {
        $this->params->can_configure_school=!file_exists(Config::SchoolConfigFile());
    }

    private function GetTables()
    {
        $result=Db::GetInstance()->query("SHOW TABLES");
        $this->params->tables=array();
        foreach ($result as $row) {
            $this->params->tables[] = $row[0];
        }
        return $this->params->tables;
    }

    public function configureSchoolAction()
    {
        $configure_school_result=$this->flash()->peek("configure_school_result");
        if(file_exists(Config::SchoolConfigFile()) && !$configure_school_result) {
            return $this->View("overwrite_school_forbidden.html.twig");
        } elseif (!$configure_school_result) {
            $this->params->schoolname = NULL;
            $this->params->title = NULL;
            return $this->View("school_config_form.html.twig");
        } elseif (!$configure_school_result->valid_config) {
            return $this->View("school_config_form.html.twig", $configure_school_result);
        } else {
            $this->flash()->clear();
            return $this->View("school_config_written.html.twig", $configure_school_result);
        }
    }

    public function submitSchoolConfigAction()
    {
        if(file_exists(Config::SchoolConfigFile())) {
            return $this->redirect($this->generateUrl("educ_action_ades_install_school"));
        } else if(!$this->SchoolConfigIsValid() || $this->WriteSchoolConfig()) {
            $this->flash()->set("configure_school_result", $this->params);
            return $this->redirect($this->generateUrl("educ_action_ades_install_school"));
        } else {
            return $this->ShowWriteError(Config::SchoolConfigFile(), $this->GetSchoolConfigSubmitUrl());
        }
    }

	private function SchoolConfigIsValid(){
		$this->params->schoolname = $_POST["schoolname"];
		$this->params->title = $_POST["title"];
        if($this->params->schoolname!=NULL && $this->params->title!=NULL) {
            $this->params->valid_config= TRUE;
        } else {
            $this->params->missing_fields = TRUE;
            $this->params->valid_config= FALSE;
        }
        return $this->params->valid_config;
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
				, var_export($this->params->schoolname, true)
				, var_export($this->params->title, true)
			);
			fclose($file);
			return true;
		}else{
			return false;
		}
	}
	private function ConfigIsValid(){
		$this->params->host=$_POST["sqlserver"];
		$this->params->username=$_POST["utilisateursql"];
		$this->params->pwd=$_POST["motdepassesql"];
		$this->params->dbname=$_POST["nomdelabasesql"];
		if(
			$this->params->host!=NULL
			&& $this->params->username!=NULL
			&& $this->params->pwd!=NULL
			&& $this->params->dbname!=NULL
		){
			$valid=Db::GetInstance($this->params->host, $this->params->username, $this->params->pwd, $this->params->dbname)->connect();
			if(!$valid) $this->params->error=Db::GetInstance()->error();
		}else{
			$valid=false;
			$this->params->missing_fields=true;
		}
        $this->params->valid_config=$valid;
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
					$this->params->error_command=$uneCommande;
					$this->params->error=Db::GetInstance()->error();
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
				$this->params->failedScript=$upgrade->failedScript;
				$this->params->error=$upgrade->failedScriptError;
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
        $error=error_get_last();
		$this->params->error=$error["message"];
        $pwuid=posix_getpwuid(posix_geteuid());
		$this->params->system_user=$pwuid["name"];
		$this->params->config_filename=realpath(DIRNAME($fname)).DIRECTORY_SEPARATOR.basename($fname);
		$this->params->resubmitAction=$resubmitAction;
        $this->params->form_values=$_POST;
        return $this->View("write_error.html.twig");
	}
}
