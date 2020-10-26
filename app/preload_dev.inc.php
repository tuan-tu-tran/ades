<?php

use Symfony\Component\Yaml\Yaml;
use EducAction\AdesBundle\Tools;
$configFile=__DIR__."/../local/dev.yml";
$ipList=NULL;
$displayErrors=TRUE;
$defaultIpList=array('127.0.0.1', 'fe80::1', '::1');
$allowAll = FALSE;
if (file_exists($configFile)) {
    $config=Yaml::Parse(file_get_contents($configFile));
    $parameters=Tools::GetDefault($config,"parameters", array());
    $ipList=Tools::GetDefault($parameters, "allowed_ips", NULL);
    $displayErrors=Tools::GetDefault($parameters,"display_errors", TRUE);
    $cacheRoot=Tools::GetDefault($parameters,"cache_root", NULL);
    $allowAll=Tools::GetDefault($parameters,"allowAll", FALSE);
} else {
    $config=array(
        "parameters"=>array(
            "allowed_ips"=>$defaultIpList,
            "allowAll" => FALSE,
            "display_errors"=>TRUE,
            "profiler.enabled"=>TRUE,
        )
    );
    file_put_contents($configFile, Yaml::Dump($config));
    chmod($configFile, 0666);
}
if(!$ipList){
    $ipList=$defaultIpList;
}

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (
    !$allowAll && (
        isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !in_array(@$_SERVER['REMOTE_ADDR'], $ipList)
    )
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$env="dev";
$debug=TRUE;
