<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

class RawDirectoryRecursor extends DirectoryRecursor {
    /**
     * @var DirectoryIterator
     */
    protected $iterator;

    public function __construct(DirectoryIterator $iterator) {
        $this->iterator = $iterator;
    }

    /**
     * @inheritdoc
     */
    public function with($init, \Closure $collapse) {
        $value = array();
        $value[0] = $init;

        $recurse = array();
        $recurse[0] = function(FSObject $obj) use ($collapse, &$value, &$recurse) {
            if ($file = $obj->toFile()) {
                $value[0] = $collapse($value[0], $file);
            } 
            else {
                $obj->toDirectory()     // This will succeed, but well, checks...
                    ->withContents()    // This returns an iterator on the directory.
                    ->onContents($recurse[0]);  // RECURSE!
            }
        };
        $this->iterator->onContents($recurse[0]);
        return $value[0];
    }
}
