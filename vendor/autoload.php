<?php
require("SplClassLoader.php");

$educActionLoader=new SplClassLoader("EducAction", "../src");
$educActionLoader->setFileExtension(".class.php");
$educActionLoader->register();
