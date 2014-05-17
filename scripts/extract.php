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
header("Content-type: text/plain");

function error($msg)
{
    die("error: $msg: ".error_get_last()["message"]);
}

function rm($path, $except)
{
    if(is_dir($path)) {
        $ls=scandir($path);
        foreach($ls as $subpath) {
            if($subpath!="." && $subpath!=".." && !in_array("$path/$subpath", $except)){
                rm($path."/".$subpath, $except) or error("could not remove $path/$subpath");
            }
        }
        if($path!=".") {
            rmdir($path) or error("could not remove $path");
        }
        return TRUE;
    } elseif (file_exists($path)) {
        unlink($path) or error("could not delete $path");
        return TRUE;
    }
}

chdir("..") or error("could not change dir to parent");
$archive="archive.zip";
if (file_exists($archive)) {
    rm(".", array("./local","./$archive")) or error("could not empty current folder ");
    
    $config_files=array(
        "web/config/confbd.inc.php",
        "web/config/constantes.inc.php",
    );
    foreach ($config_files as $file) {
        $dst="local/".basename($filename);
        if (file_exists($file) && !file_exists($dst)) {
            rename($file, $dst) or error("could not move $file to local");
        }
    }

    if ($renamed_local=file_exists("local")) {
        rename("local","local_bkp") or error("could not rename local to local_bkp");
    }
    $zip=new ZipArchive;
    $zip->open("archive.zip") or error("could not open archve");
    $zip->extractTo(".") or error("could not extract archive");
    if($renamed_local) {
        rm("local") or error("could not delete local");
        rename("local_bkp","local") or error("could not rename local to local_bkp");
    }
    rm($archive) or error("could not delete $archive");
    echo "archive extracted";
}else{
    die("error: no archive");
}
