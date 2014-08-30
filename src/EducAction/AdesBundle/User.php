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

class User{
    const ACCESS_ADMIN="admin";
    private static $loggedOut = FALSE;

    public static function logout(){
        self::$loggedOut=TRUE;
        unset($_SESSION["identification"]);
    }

	public static function IsLogged(){
		return isset($_SESSION["identification"]["user"]) && !self::$loggedOut;
	}

    public static function isAdmin()
    {
        return self::HasAccess("admin");
    }

	public static function HasAccess(){
		if(isset($_SESSION['identification']['privilege'])){
			$required_access = func_get_args();
			// si aucun utilisateur n'a été désigné, tout le monde est autorisé
			if (count($required_access) == 0) return true;
			// l'utilisateur actuel fait-il partie de la liste?
			$user = $_SESSION['identification']['privilege'];
			return in_array($user, $required_access);
		}else
			return false;
	}

	public static function CheckIfLogged(){
		if(!User::IsLogged()){
			Tools::Redirect("accueil.php");
		}
		return true;
	}

	public static function CheckAccess(){
		if(!call_user_func_array(array("EducAction\\AdesBundle\\User","HasAccess"), func_get_args()))
			Tools::Redirect("unauthorized.php");
	}

	public static function GetId(){
		return $_SESSION["identification"]["idedu"];
	}
}
