<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

require_once("tests/autoloader.php");

$adapter = new \League\Flysystem\Adapter\Local(__DIR__);
$flysystem = new \League\Flysystem\Filesystem($adapter);
$flightcontrol_strict = new \Lechimp\Flightcontrol\Flightcontrol($flysystem);
$flightcontrol_lazy = new \Lechimp\Flightcontrol\Flightcontrol($flysystem, false);

$dir_strict = $flightcontrol_strict->directory("");
$dir_lazy = $flightcontrol_lazy->directory("");

foreach (array("STRICT" => $dir_strict, "LAZY" => $dir_lazy) as $what => $dir) {
    echo "$what:\n\n";
    $start = microtime(true);
    $arr = array();
    for ($i = 0; $i < 100; $i++) {
        $foo = $dir
        ->recurseOn()
        ->filter(function ($obj) {
            return $obj->name() == "" || in_array(substr($obj->name(), 1), array("c", "1", "r", "s"));
        })
        ->with(function ($obj) use (&$arr) {
            // force the lazy fdirectory.
            if (!$obj->isFile()) {
                $obj->fcontents();
            }
            $arr[] = $obj->name();
            return $obj;
        });
        $foo->fcontents();
    }
    $end = microtime(true);

    echo "Counted " . count($arr) . " elements.\n" .
         "Took " . ($end - $start) . " seconds\n" .
         "\n\n\n";
}
