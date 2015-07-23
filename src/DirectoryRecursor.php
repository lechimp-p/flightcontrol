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
abstract class DirectoryRecursor {
    use NamedFilterTrait;

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
     * Give an initial value of a any type and a function from that type and
     * File to another value of the type. Will then recursively feed any file 
     * below the directory and the successive values to the function.
     *
     * Returns the value that is retreived from the function after the last
     * File was given to it.
     *
     * @example
     *
     * \\ Collect all file names in a directory:
     * $init = array();     \\ Start with an empty array
     * $function = function($array, $file) { 
     *      $array[] = $file->name();
     *      return $array();
     * };
     *      
     * $result = $directory->foldFiles()->with($init, $function);
     * // $result will be the names of all files in $directory and the directories
     * // below it.
     *
     * @param mixed         $init
     * @param \Closure      $collapse   (mixed, File) -> mixed 
     * @return mixed 
     */
    abstract public function with($init, \Closure $collapse);
}
