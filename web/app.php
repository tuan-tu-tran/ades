<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.
/*
$apcLoader = new ApcClassLoader('sf2', $loader);
$loader->unregister();
$apcLoader->register(true);
*/
if(isset($preload) && $preload) {
    if(file_exists($preload)) {
        require $preload;
    } else {
        header("HTTP/1.0 500 Internal server error");
        exit("The file $preload does not exist.");
    }
}

require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/AppCache.php';

$env=isset($env)?$env:'prod';
$debug=isset($debug)?$debug:FALSE;

$kernel = new AppKernel($env, FALSE);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
