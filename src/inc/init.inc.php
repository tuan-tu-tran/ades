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
/*
 * Author: Tuan-Tu Tran
 *
 * This file contains basic initializations and should
 * be safely included at the begining of any controller
 */

if(!defined("_INIT_INCLUDED_")){
	define("_INIT_INCLUDED_","");
	define("_CONFIG_FOLDER_FULL_PATH_",realpath(DIRNAME(__FILE__)."/../config").DIRECTORY_SEPARATOR);
	define('_DB_CONFIG_FILE_',_CONFIG_FOLDER_FULL_PATH_.'confbd.inc.php');
	define('_SCHOOL_CONFIG_FILE_',_CONFIG_FOLDER_FULL_PATH_.'constantes.inc.php');
	define("_CLASS_DIR_",DIRNAME(__FILE__)."/../classes");
	define("_VIEWS_FOLDER",DIRNAME(__FILE__)."/../views");

	require("inc/fonctions.inc.php");
	@include_once(_SCHOOL_CONFIG_FILE_);

	set_include_path(_CLASS_DIR_);
	spl_autoload_register(function($classname){
		if(!class_exists($classname)){
			include($classname.".class.php");
		}
	});
}
