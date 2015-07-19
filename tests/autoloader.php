<?php
/******************************************************************************
 * DESCRIPTION
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

spl_autoload_register(function($classname) {
    if ($classname == "_TestCaseBase") {
        require_once("tests/_TestCaseBase.php");
        return;
    }

    $parts = explode("\\", $classname);
    // remove Lechimp\\Flightcontrol
    array_shift($parts);
    array_shift($parts);

    $path = "src/".implode("/", $parts).".php";
    if (file_exists($path)) {
        require_once($path);
    }
});

require_once("vendor/autoload.php");

?>
