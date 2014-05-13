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

use Exception;

class View{
	const REPLACE="replace";
	const PREPEND="prepend";
	const APPEND="append";

	public static function Render($template, $parameters=NULL){
		$template=self::GetTemplateFile($template);
		if(file_exists($template)){
			if($parameters!=NULL){
				if(!is_array($parameters)) $parameters=get_object_vars($parameters);
				extract($parameters);
			}
			require($template);
		}else throw new Exception("template not found '$template'");

	}

	public static function Embed($template, $parameters=NULL){
        self::Render($template);
	}

	private static function GetTemplateFile($template)
	{
		return DIRNAME(__FILE__)."/Resources/views/$template";
	}

	private static $current_block=NULL;
	private static $slots=array();

	public static function StartBlock($block_name){
		if(!$block_name){
			throw new Exception("cannot start an block with no name");
		}
		if(self::$current_block){
			$current_block=self::$current_block;
			throw new Exception("cannot start a new block '$block_name' because already in a block '$current_block'");
		}
		self::$current_block=$block_name;
		ob_start();
	}

	public static function EndBlock($mode=self::REPLACE){
		if(!self::$current_block){
			throw new Exception("no block to end");
		}
		$contents=ob_get_contents();
		ob_end_clean();
		if(isset(self::$slots[self::$current_block]))
			$current=self::$slots[self::$current_block];
		else
			$current="";
		switch($mode){
			case self::REPLACE:
				$newContent=$contents;
				break;
			case self::PREPEND:
				$newContent=$contents.$current;
				break;
			case self::APPEND:
				$newContent=$current.$contents;
				break;
			default:
				throw new Exception("mode received : $mode");
		}
		self::$slots[self::$current_block]=$newContent;
		self::$current_block=NULL;
	}

	public static function Block($name){
		if(isset(self::$slots[$name]))
			echo self::$slots[$name];
	}

	public static function FillBlock($name, $value){
		self::$slots[$name]=$value;
	}
}
