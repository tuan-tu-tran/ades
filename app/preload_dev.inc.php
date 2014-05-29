<?php

use Symfony\Component\Yaml\Yaml;
$ipFile=__DIR__."/../local/ip_dev.yml";
$ipList=NULL;
if (file_exists($ipFile)) {
    $ipList=Yaml::Parse(file_get_contents($ipFile));
}
if(!$ipList){
    $ipList=array('127.0.0.1', 'fe80::1', '::1');
}

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], $ipList)
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$env="dev";
$debug=TRUE;
