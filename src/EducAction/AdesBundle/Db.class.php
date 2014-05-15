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

namespace EducAction\AdesBundle;

use mysqli;

class Db{
	public $host;
	public $user;
	public $pwd;
	public $dbname;
	private $conn;
	private $connect_error;
	private static $instance;

	private function __construct($host, $user, $pwd, $dbname){
		$this->host=$host;
		$this->username=$user;
		$this->pwd=$pwd;
		$this->dbname=$dbname;
		$this->conn=NULL;
		$this->connect_error=NULL;
	}

	public function __destruct(){
		if($this->conn!=NULL){
			$this->conn->close();
		}
	}

	public function connect(){
		if($this->conn==NULL){
			$conn=new mysqli($this->host, $this->username, $this->pwd);
			if(!$conn->connect_errno){
				$this->connect_error=NULL;
				if($conn->select_db($this->dbname))
				{
					$this->conn=$conn;
				}
				else
				{
					$this->connect_error=$conn->error;
				}
			}else{
				$this->connect_error=$conn->connect_error;
			}
		}
		return $this->conn!=NULL;
	}

    /**
     * Execute a query that returns no result (DbException thrown in case of error)
     */
    public function execute($query)
    {
        $this->private_execute_or_throw($query, $result);
	}

    /**
     * Execute a query without result and return whether succesfully done.
     */
    public function TryExecute($query, &$error=NULL)
    {
        if (!$this->private_safe_execute($query, $result)) {
            $error=$this->error();
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Execute a query and throw an exception in case of error.
     */
    private function private_execute_or_throw($query, &$result)
    {
        if (!$this->private_safe_execute($query, $result)) {
            throw new DbException($this->error());
        }
    }

    /**
     * Execute a query and return if done + the resut
     */
	private function private_safe_execute($query, &$result){
		if($this->connect()){
			$result=$this->conn->query($query);
			return !$this->conn->errno;
		}
		return false;
	}

	public function query($query){
        $this->private_execute_or_throw($query,$result);
        return self::GetDataTableFromResultInstance($result);
	}
    
    /**
     * Execute a query and return if succesfully done.
     * If executed, $result is assigned the query result.
     * Otherwise, $error is assigned the last error.
     */
    public function TryQuery($query, &$result, &$error=NULL)
    {
        if ($this->private_safe_execute($query, $result)) {
            $result=self::GetDataTableFromResultInstance($result);
            return TRUE;
        } else {
            $result=NULL;
            $error = $this->error();
            return FALSE;
        }
    }

    private static function GetDataTableFromResultInstance($result)
    {
        if (method_exists($result,"fetch_all")) {
            $res = $result->fetch_all(MYSQLI_BOTH);
        } else {
            for ($res = array(); $tmp = $result->fetch_array(MYSQLI_BOTH);) {
                $res[] = $tmp;
            }
        }
        return $res;
    }

	public function scalar($query){
        $this->private_execute_or_throw($query,$result);
        if ($result->num_rows>0) {
            return $result->fetch_row()[0];
        } else {
            return NULL;
        }
	}

	public function error(){
		if($this->connect_error!=NULL) return $this->connect_error;
		if($this->conn!=NULL && $this->conn->errno) return $this->conn->error;
		return "";
	}

	public static function GetInstance($host=NULL, $user=NULL, $pwd=NULL, $dbname=NULL){
		if(Db::$instance == NULL){
			if($host==NULL){
				$configFile = Config::DbConfigFile();
				if(!file_exists($configFile)){
					throw new \Exception("db config file does not exist: $configFile");
				}
				require $configFile;
				Db::$instance=new Db($sql_serveur, $sql_user, $sql_passwd, $sql_bdd);
			}else{
				Db::$instance=new Db($host,$user,$pwd,$dbname);
			}
		}
		return Db::$instance;
	}

	public function escape_string($s){
		if($this->connect()){
			return $this->conn->escape_string($s);
		}else{
			throw new DbException("could not connect to escape string '$s' : ".($this->error()));
		}
	}
}
