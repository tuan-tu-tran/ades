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

class Process{
	public static function Execute($cmd, $in, &$out, &$err, &$retval){
		$inoutdesc=array(
			0=>array("pipe","r"),
			1=>array("pipe","w"),
			2=>array("pipe","w"),
		);
		if($proc=proc_open($cmd,$inoutdesc, $pipes)){
			if($in){
				fwrite($pipes[0], $in);
			}
			fclose($pipes[0]);
			$out=stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$err=stream_get_contents($pipes[2]);
			fclose($pipes[2]);
			$retval=proc_close($proc);
			return true;
		}
		else{
			return false;
		}
	}
}
