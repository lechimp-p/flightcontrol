<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

/**
* This object can perform a recursion on the files in a directory.
* I don't know if the name really fits.
*/
class DirectoryRecursor {
    use NamedFilterTrait;

    /**
     * @var DirectoryIterator|DirectoryRecursor
     */
    protected $previous;

    public function __construct(DirectoryIterator $iterator) {
        $this->previous = $iterator;
    }

    /**
     * Get a recursor that folds all files in this recursor that match the
     * provided predicate.
     *
     * @param  \Closure             $predicate  (FSObject -> Bool)
     * @return DirectoryRecursor
     */
    public function filter(\Closure $predicate) {
        return new FilterDirectoryRecursor($predicate, $this);
    }

    /**
     * Finally collaps the files fitting this recursor. 
     *
     * Give an initial value of a type $start_t and a function from $start_t and
     * File to another $start_t. Will then recursively feed any file below the
     * directory and the successive $start_t values to the function.
     *
     * @param $start_t      $init
     * @param \Closure      $collapse   ($start_t, File) -> $start_t
     * @return $start_t
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
        $this->previous->onContents($recurse[0]);
        return $value[0];
    }
}
