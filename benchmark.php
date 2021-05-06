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

$dir = $flightcontrol_strict->directory("");

$runs = 100;
$count = 0;

$start = microtime(true);
for($i = 0; $i < $runs; $i++) {
    $count = 0;
    $dir
        ->iterateOn()
            ->iterateOn()
            ->with(function($obj) use (&$count) {
                $count += 1;  
            });
}
$end = microtime(true);
$diff = $end - $start;

echo "Flightcontrol: ".sprintf("%10.4f", $diff)." s (and counted $count)\n";

$start = microtime(true);
for ($i = 0; $i < $runs; $i++) {
    $count = 0;
    foreach($flysystem->listContents() as $dir) {
        foreach($flysystem->listContents($dir["path"]) as $dir2) {
            $count += 1;
        }
    }
}
$end = microtime(true);
$diff = $end - $start;

echo "Flysystem:     ".sprintf("%10.4f", $diff)." s (and counted $count)\n";

$start = microtime(true);
for ($i = 0; $i < $runs; $i++) {
    $count = 0;
    foreach(scandir(".") as $dir) {
        if (!is_dir($dir) or $dir == "." or $dir == "..") {
            continue;
        }
        foreach(scandir("./$dir") as $dir2) {
            if ($dir2 == "." or $dir2 == "..") {
                continue;
            }
            $count += 1;
        }
    }
}
$end = microtime(true);
$diff = $end - $start;

echo "scandir:       ".sprintf("%10.4f", $diff)." s (and counted $count)\n";


