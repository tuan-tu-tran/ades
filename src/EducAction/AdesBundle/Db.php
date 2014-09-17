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
    private $stmt_error;
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
    public function execute($query/*, $params, ... or $paramsArray*/)
    {
        $this->private_execute_or_throw(func_get_args(), $stmt);
        $stmt->close();
	}

    /**
     * Execute a query without result and return whether succesfully done.
     */
    public function TryExecute($query/*, $params, ... or $paramsArray*/)
    {
        if (!$this->private_safe_execute(func_get_args(), $stmt)) {
            return FALSE;
        }
        $stmt->close();
        return TRUE;
    }

    /**
     * Execute an insert query and return the last inserted id or throw a DbException
     */
    public function insert($query)
    {
        $this->execute($query);
        $result=$this->query("SELECT LAST_INSERT_ID()");
        $row=$result[0];
        return $row[0];
    }

    /**
     * Execute a query and throw an exception in case of error.
     */
    private function private_execute_or_throw($args, &$stmt)
    {
        if (!$this->private_safe_execute($args, $stmt)) {
            throw new DbException($this->error());
        }

    }

    /**
     * Execute a query and return if done + the resut
     */
	private function private_safe_execute($args, &$stmt){
		if($this->connect()){
            $this->getQueryParams($args, $query, $queryParams);
            $params=NULL;
            if(count($queryParams)>0){
                $values=array();
                $types="";
                $i=0;
                foreach($queryParams as &$value){
                    if(is_int($value)){
                        $types.="i";
                    } elseif (is_float($value)) {
                        $types.="d";
                    } elseif (is_string($value)) {
                        $types.="s";
                    } elseif (is_bool($value)) {
                        $types.="i";
                        $value=$value?1:0;
                    } elseif (is_a($value, "\\Datetime")){
                        $types.="s";
                        $value=$value->format("Y-m-d H:i:s");
                    } else {
                        throw new DbException("unsupported parameter type ".$i);
                    }
                    ++$i;
                    $values[]=&$value;
                }
                $params=array($types);
                foreach($values as &$value){
                    $params[]=&$value;
                }
            }
            $stmt = $this->conn->prepare($query);
            if(!$stmt){
                return FALSE;
            }
            if($params){
                if(!call_user_func_array(array($stmt, "bind_param"), $params)) {
                    $this->setStatementError($stmt);
                    return FALSE;
                }
            }
            if(!$stmt->execute()){
                $this->setStatementError($stmt);
                return FALSE;
            }
			return !$this->conn->errno;
		}
		return false;
	}

    private function setStatementError($stmt)
    {
        $this->stmt_error = $stmt->error;
        $stmt->close();
    }

    private function getQueryParams(&$args, &$query, &$params)
    {
        $query=array_shift($args);
        if (count($args) == 1 && is_array($args[0])) {
            $params=$args[0];
        } else {
            $params=$args;
        }

    }

    /**
     * Execute a query a return results or throw an exception
     */
	public function query($query/*, $params, ... or $paramsArray*/){
        $this->private_execute_or_throw(func_get_args(), $stmt);
        return self::GetDataTableFromStatement($stmt);
	}
    
    /**
     * Execute a query and return if succesfully done.
     * If executed, $result is assigned the query result.
     */
    public function TryQuery(&$result, $query/*, $params, ... or $paramsArray*/)
    {
        $args=func_get_args();
        array_shift($args);
        if ($this->private_safe_execute($args, $stmt)) {
            $result=self::GetDataTableFromStatement($stmt);
            return TRUE;
        } else {
            $result=NULL;
            return FALSE;
        }
    }

    private static function GetDataTableFromStatement($stmt, $onlyOne=FALSE)
    {
        if(method_exists($stmt, "get_result")){
            $result=$stmt->get_result();
            if(!$result){
                throw new DbException("could get result from statement");
            }
            $result=self::GetDataTableFromResultInstance($result);
            $stmt->close();
            return $result;
        } else {
            $metadata=$stmt->result_metadata();
            if(!$metadata){
                $this-setStatementError($stmt);
                throw new DbException("could not get statement metadata: ".$stmt->error);
            }
            $result=array();
            $indexedRow=array();
            $row=array();
            $fields=$metadata->fetch_fields();
            foreach($fields as $f){
                $row[$f->name]=NULL;
                $row[]=&$row[$f->name];
                $indexedRow[]=&$row[$f->name];
            }
            call_user_func_array(array($stmt,"bind_result"), $indexedRow);
            while($stmt->fetch()){
                $resultRow=array();
                foreach($fields as $f){
                    $resultRow[$f->name]=$row[$f->name];
                    $resultRow[]=&$resultRow[$f->name];
                }
                $resut[]=$resultRow;
                if($onlyOne){
                    break;
                }
            }
            return $resut;
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

	public function scalar($query/*, $params, ... or $paramsArray*/){
        $this->private_execute_or_throw(func_get_args(), $stmt);
        $result=self::GetDataTableFromStatement($stmt, TRUE);
        if($result){
            $row=$result[0];
            return $row[0];
        } else {
            return NULL;
        }
	}

	public function error(){
		if($this->connect_error!=NULL) return $this->connect_error;
		if($this->conn!=NULL && $this->conn->errno) return $this->conn->error;
        if($this->stmt_error) return $this->stmt_error;
        error_log("here");
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
			return "'".$this->conn->escape_string($s)."'";
		}else{
			throw new DbException("could not connect to escape string '$s' : ".($this->error()));
		}
	}

    /**
     * return $n parameters markers, separated by commas enclosed in brackets and spaces.
     * e.g.: " (?,?,?,...,?) "
     */
    public static function getWhereInClause($n)
    {
        return " (".implode(array_fill(0,$n,"?"), ",").") ";
    }
}
