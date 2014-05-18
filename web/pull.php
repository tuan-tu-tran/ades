<?php
/**
 * Copyright (c) 2014 Tuan-Tu TRAN
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

require "inc/init.inc.php";

use EducAction\AdesBundle\Config;

$url_file=Config::LocalFile("pull_url.txt");
if (file_exists($url_file)) {
    $url=trim(file_get_contents($url_file));
    if($url) {
        $archive_content=file_get_contents($url);
        if ($archive_content) {
            file_put_contents("../archive.zip", $archive_content);
            copy("zip://../archive.zip#scripts/extract.php", "extract.php");
            require "extract.php";
        }
    }
}
