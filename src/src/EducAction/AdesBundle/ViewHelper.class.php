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
class ViewHelper{
	const GB=1073741824; //1024*1024*1024
	const MB=1048576; //1024*1024
	const KB=1024;
	public static function FileSize($size,$dec=0,$byte="o"){
		if($size>=self::GB){
			echo sprintf("%.".$dec."f G%s", $size/self::GB, $byte);
		}else if($size>=self::MB){
			echo sprintf("%.".$dec."f M%s", $size/self::MB, $byte);
		}else if($size>=self::KB){
			echo sprintf("%.".$dec."f K%s", $size/self::KB, $byte);
		}else{
			echo sprintf("%d %s", $size, $byte);
		}
	}
}
