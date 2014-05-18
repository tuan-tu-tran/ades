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

header("Content-Type: text/plain");
require "inc/init.inc.php";

use EducAction\AdesBundle\Config;
use EducAction\AdesBundle\Tools;

$url_file=Config::LocalFile("pull_url.txt");
$secret_file=Config::LocalFile("pull_secret.txt");
if (!file_exists($url_file)) {
    http_response_code(500);
    echo "no pull url file";
} elseif ( ( $url=file_get_contents($url_file) ) === FALSE ) {
    http_response_code(500);
    echo "could not read pull url: ".Tools::GetLastError();
} elseif (!$url=trim($url)) {
    http_response_code(500);
    echo "no pull url";
} elseif (!file_exists($secret_file)) {
    http_response_code(500);
    echo "no pull secret file";
} elseif ( ($secret=file_get_contents($secret_file)) === FALSE ) {
    http_response_code(500);
    echo "could not read pull secret: ".Tools::GetLastError();
} elseif (!$secret=trim($secret)) {
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
    } else {
        echo "OK\n";
        var_dump($data);
        /*
            $archive_content=file_get_contents($url);
            if ($archive_content) {
                file_put_contents("../archive.zip", $archive_content);
                copy("zip://../archive.zip#scripts/extract.php", "extract.php");
                require "extract.php";
            }
        */
    }
}
