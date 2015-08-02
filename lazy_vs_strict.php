<?php

require_once("tests/autoloader.php");

$adapter = new \League\Flysystem\Adapter\Local(__DIR__);
$flysystem = new \League\Flysystem\Filesystem($adapter);
$flightcontrol = new \Lechimp\Flightcontrol\Flightcontrol($flysystem);
$flightcontrol_lazy = new \Lechimp\Flightcontrol\Flightcontrol($flysystem, false);

$dir = $flightcontrol->get("");
$dir_lazy = $flightcontrol_lazy->get("");

$measure = function($dir) {
    $start = microtime(true);
    $arr = array();
    for ($i = 0; $i < 100; $i++) {
        $foo = $dir
        ->recurseOn()
        ->filter(function($obj) {
            return $obj->name() == "" || in_array(substr($obj->name(), 1), array("c", "1", "r", "s"));
        })
        ->with(function($obj) use (&$arr) {
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
    echo "\n\n\n".
         "Counted ".count($arr)." elements.\n".
         "Took ".($end - $start)." seconds\n".
         "\n";
};

echo "STRICT:\n";
$measure($dir);
echo "\n\nLAZY:\n";
$measure($dir_lazy);
