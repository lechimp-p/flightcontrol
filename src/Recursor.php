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
class Recursor extends FSObject {
    use NamedFilterTrait;

    /**
     * @var Directory
     */
    protected $directory;

    public function __construct(Directory $directory) {
        parent::__construct($directory->flightcontrol, $directory->filesystem, $directory->path);
        $this->directory = $directory;
    }

    /**
     * Get a recursor that folds all files in this recursor that match the
     * provided predicate and the objects below that match the predicate as
     * well.
     *
     * Won't recurse over directories that do not match the predicate!
     *
     * @param  \Closure             $predicate  (FSObject -> Bool)
     * @return Recursor
     */
    public function filter(\Closure $predicate) {
        return new FilteredRecursor($this, $predicate);
    }

    /**
     * Get a functor representation of this recursor.
     *
     * Also see documentation of FDirectory.
     * 
     * @return FDirectory
     */
    public function unfix() {
        $new_contents = array_map(function(FSObject $obj) {
            $file = $obj->toFile();
            if ($file !== null) {
                return $file;
            }
            assert($obj instanceof Directory);
            return $this->copyOnDirectory($obj);
        }, $this->contents());

        return new FDirectory($this, $new_contents); 
    }

    /**
     * We could also use the catamorphism on this to do recursion, as we
     * have an unfix and an underlying fmap from the FDirectory.
     *
     * Supply a function $trans from File|FDirectory a to a that flattens 
     * (folds) a directory. Will start the directories where only files are 
     * included, folds them and then proceeds upwards.
     * 
     * The return type should be 'a' (from the function $trans) instead 
     * of mixed, but we can't express that fact correctly in the docstring
     * typing.
     *
     * @param   \Closure    $trans      File|FDirectory a -> a
     * @return  mixed
     */
    public function cata(\Closure $trans) {
        return $trans( $this->unfix()->fmap(function(FSObject $obj) use ($trans) {
            $file = $obj->toFile();
            if ($file !== null) {
                return $trans($file);
            }
            assert($obj instanceof Directory || $obj instanceof Recursor);
            return $obj->cata($trans);
        }));
    }

    /**
     * Sugar for cata.
     *
     * @param   \Closure    $trans      File|FDirectory a -> a
     * @return  mixed
     */
    public function with(\Closure $trans) {
        return $this->cata($trans);
    }

    /**
     * Fold over all files in this directory and subjacent directories.
     *
     * Start with an initial value of some type and a function from that type
     * and File to a new value of the type. Will successively feed all files
     * and the resulting values to that function.
     *
     * @param   mixed       $start_value
     * @param   \Closure    $fold_with      a -> File -> a
     * @return  Recursor 
     */
    public function foldFiles($start_value, \Closure $fold_with) {
        foreach ($this->allFiles() as $file) {
            $start_value = $fold_with($start_value, $file);
        }
        return $start_value;
    }

    /**
     * Get a list of all files in the directory and subjacent directories.
     *
     * @return File[]
     */
    public function allFiles() {
        return $this->cata(function(FSObject $obj) {
            $file = $obj->toFile();
            if ($file !== null) {
                return array($file);
            }
            assert($obj instanceof FSObject);
            $fcontents = $obj->fcontents();
            if (empty($fcontents)) {
                return $fcontents;
            }
            return call_user_func_array("array_merge", $fcontents);
        });
    }
     

    /**
     * @inheritdoc
     */
    public function isFile() {
        return false;
    }

    // Helpers

    /**
     * Create a copy of this recursor, but on a different path.
     */
    protected function copyOnDirectory(Directory $directory) {
        return new Recursor($directory);
    }

    /**
     * Get the contents from the directory of this recursor
     */
    protected function contents() {
        return $this->directory->contents();
    }

    /**
     * Get the directory from the recursor.
     */
    protected function directory() {
        return $this->directory;
    }
}