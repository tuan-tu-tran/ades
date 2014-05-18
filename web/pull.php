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
namespace EducAction\AdesBundle\Pull;

header("Content-Type: text/plain");
ini_set("display_errors",0);
function output_last_error()
{
    $last=error_get_last();
    if($last) {
        echo "\n\nlast error: ".$last["message"]." in ".$last["file"]." at line ".$last["line"];
    }
}
//so that we get 500 error code but still see the error message
register_shutdown_function("\EducAction\\AdesBundle\\Pull\\output_last_error");

function unroot($archive)
{
    $zip=new \ZipArchive;
    $zip->open($archive);
    $roots=array();
    for($i=0; $i<$zip->numFiles; ++$i){
        $filename=$zip->GetNameIndex($i);
        $root=explode("/",$filename);
        $root=$root[0]."/";
        $roots[$root]=NULL;
    }

    if (count($roots)==1){
        $roots=array_keys($roots);
        $root=$roots[0];
        for($i=0; $i<$zip->numFiles; ++$i){
            $filename=$zip->GetNameIndex($i);
            if($filename == $root){
                $zip->deleteIndex($i);
            }else{
                $zip->renameIndex($i, substr($filename, strlen($root), strlen($filename)-strlen($root)));
            }
        }
    }
    $zip->close();
}

require "inc/init.inc.php";

use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\Tools;

$url_file=Config::LocalFile("pull_url.txt");
$secret_file=Config::LocalFile("pull_secret.txt");
$pull_config_file=Config::LocalFile("pull.ini");
if (!file_exists($pull_config_file)) {
    http_response_code(500);
    echo "no pull config file";
} elseif ( ( $pull_config=parse_ini_file($pull_config_file) ) === FALSE ) {
    http_response_code(500);
    echo "could not read pull config: ".Tools::GetLastError();
} elseif (!Tools::TryGet($pull_config, "url", $url) || !$url) {
    http_response_code(500);
    echo "no pull url";
} elseif (!Tools::TryGet($pull_config, "ref", $pull_ref) || !$pull_ref) {
    http_response_code(500);
    echo "no pull reference";
} elseif (!Tools::TryGet($pull_config, "secret", $secret) || !$secret) {
    http_response_code(500);
    echo "no pull secret";
} elseif (!Tools::TryGet($_SERVER, "HTTP_X_HUB_SIGNATURE", $signatureHeader)) {
    http_response_code(401);
    echo "no signature header";
} elseif (count($signature_data=explode("=",$signatureHeader)) != 2) {
    http_response_code(400);
    echo "wrong signature format: $signatureHeader";
} else {
    $signature=$signature_data[1];
    $algo=$signature_data[0];
    $data=file_get_contents("php://input");
    $hmac=hash_hmac($algo, $data, $secret);
    if($hmac === FALSE) {
        http_response_code(501);
        echo "hash method not supported: $algo";
    } elseif ($hmac != $signature) {
        http_response_code(401);
        echo "wrong signature";
    } elseif (!$json=json_decode($data)){
    } elseif (!isset($json->ref)) {
    } elseif ($json->ref!=$pull_ref){
        echo "ignoring ref: ".$json->ref." : only interested in $pull_ref";
    } else {
        $archive_content=file_get_contents($url);
        if (!$archive_content) {
            http_response_code(500);
            echo "could not get archve at $url";
        } elseif (!file_put_contents("../archive.zip", $archive_content)) {
            http_response_code(500);
            echo "could not write archive";
        } else {
            //unroot the archive
            unroot("../archive.zip");
            if (!copy("zip://../archive.zip#scripts/extract.php", "extract.php")) {
                http_response_code(500);
                echo "could not extract extract.php from archive";
            } else {
                require "extract.php";
            }
        }
    }
}
