<?php
/******************************************************************************
 * DESCRIPTION
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

spl_autoload_register(function($className) {
    $parts = explode("\\", $className);
    // remove Lechimp\\??
    array_shift($parts);
    array_shift($parts);

    $path = "src/".implode("/", $parts).".php";
    if (file_exists($path)) {
        require_once($path);
    }
});

?>
