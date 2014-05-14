<?php
require("SplClassLoader.php");

$educActionLoader=new SplClassLoader("EducAction", "../src");
$educActionLoader->setFileExtension(".class.php");
$educActionLoader->register();

$oldClassesLoader=new EducAction\AdesBundle\ClassLoader(
    DIRNAME(__FILE__)."/../web",
    array(
        "prototypeFait"=>"inc/classes/classDescriptionFait.inc.php",
    )
);
$oldClassesLoader->Register();
