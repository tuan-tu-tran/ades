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

class Install{
	const ACTION_INFO="info";
	const ACTION_CONFIG_DB="configure_db";
	const ACTION_SUBMIT_DB_CONFIG="write_db_config";
	const ACTION_CREATE_TABLES="create_tables";

	const VIEW_INFO=0;
	const VIEW_DB_CONFIG_FORM=1;
	const VIEW_FILE_WRITTEN=2;
	const VIEW_FILE_NOT_WRITTEN=3;
	const VIEW_INVALID_CONFIG_SUBMITTED=4;
	const VIEW_TABLES_CREATED=5;
	const VIEW_TABLES_NOT_CREATED=6;
	const VIEW_OVERWRITE_FORBIDDEN=7;

	public function parseRequest(){
		//get the action
		$action = isset($_GET['action'])?$_GET['action']:self::ACTION_INFO;

		switch($action){
			case self::ACTION_INFO:
				$this->view=self::VIEW_INFO;
				break;

			case self::ACTION_CONFIG_DB:
				if(file_exists(_DB_CONFIG_FILE_))
					$this->view=self::VIEW_OVERWRITE_FORBIDDEN;
				else{
					//show config form
					$this->host=NULL;
					$this->username=NULL;
					$this->pwd=NULL;
					$this->dbname=NULL;
					$this->view=self::VIEW_DB_CONFIG_FORM;
				}
				break;

			case self::ACTION_SUBMIT_DB_CONFIG:
				if(file_exists(_DB_CONFIG_FILE_))
					$this->view=self::VIEW_OVERWRITE_FORBIDDEN;
				else if($this->ConfigIsValid()){
					if($this->WriteDbConfig()){
						//show config file successfully written
						$this->view=self::VIEW_FILE_WRITTEN;
					}else{
						//show file could not be written + error
						$this->error=error_get_last()["message"];
						$this->system_user=posix_getpwuid(posix_geteuid())["name"];
						$this->config_filename=_DB_CONFIG_FILE_;
						$this->view=self::VIEW_FILE_NOT_WRITTEN;
					}
				}else{
					//show config form + error + repopulate
					$this->view=self::VIEW_INVALID_CONFIG_SUBMITTED;
				}
				break;

			case self::ACTION_CREATE_TABLES:
				if($this->CreateTables()){
					//show tables created
					$this->view=self::VIEW_TABLES_CREATED;
				}else{
					//show creation failure
					$this->view=self::VIEW_TABLES_NOT_CREATED;
				}
				break;
			default:
				$this->view=self::VIEW_INFO;
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
			$this->missing_fields=false;
			$valid=Db::GetInstance($this->host, $this->username, $this->pwd, $this->dbname)->connect();
			if(!$valid) $this->error=Db::GetInstance()->error();
		}else{
			$valid=false;
			$this->missing_fields=true;
		}
		return $valid;
	}

	private function CreateTables(){
		$commandes = file("./creation.sql");
		$uneCommande = "";
		foreach ($commandes as $uneLigne){
			// supprimer les commentaires dans le fichier .sql
			if (substr($uneCommande, 0, 2) == "--")
				$uneCommande = "";
			$uneCommande .= trim($uneLigne);
			$longueur = strlen($uneCommande);
			$dernier = substr($uneCommande, $longueur-1, 1);
			if ($dernier == ";"){
				if(!Db::GetInstance()->execute($uneCommande)){
					$this->error_command=$uneCommande;
					$this->error=Db::GetInstance()->error();
					return false;
				}
				$uneCommande = "";
			}
		}
		return true;
	}

	function WriteDbConfig(){
		// Rami Adrien création du fichier confdb.inc.php
		$fichierconfdb = fopen(_DB_CONFIG_FILE_,"wt");
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

	private function GetLink($action, $text){ echo "<a href='".$this->GetUrl($action)."'>".$text."</a>"; }
	private function GetUrl($action){ return "creation.php?action=".$action; }
	public function GetDbConfigLink($text){ $this->GetLink(self::ACTION_CONFIG_DB, $text);}
	public function GetCreateTableLink($text){ $this->GetLink(self::ACTION_CREATE_TABLES, $text);}
	public function GetDbConfigSubmitUrl(){return $this->GetUrl(self::ACTION_SUBMIT_DB_CONFIG);}
}
