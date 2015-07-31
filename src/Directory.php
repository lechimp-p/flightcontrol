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
 * This class represents a directory in the FS.
 */
class Directory extends FSObject {
    /**
     * Get the objects inside the directory.
     *
     * @return FSObject[]
     */
    public function contents() {
        $contents = $this->filesystem->listContents($this->path());
        $returns = array();
        foreach ($contents as $content) {
            $returns[] = $this->flightcontrol->get($content["path"]);
        }
        return $returns;
    }

    /**
     * Only regard contents that match the predicate.
     * 
     * @param   \Closure    $predicate  File|Directory -> bool
     * @return  Directory
     */
    public function filter(\Closure $predicate) {
        return new FilteredDirectory($this, $predicate); 
    }

    /**
     * @inheritdoc
     */
    public function mimetype() {
        return "directory";
    }

    /**
     * @inheritdoc
     */
    public function isFile() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function toDirectory() {
        return $this;
    }
    /**
     * See documentation of FDirectory.
     * 
     * @return FDirectory
     */
    public function unfix() {
        return new FDirectory($this, $this->contents());
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
        return $this->recurseOn()->cata($trans);
    }

    /**
     * Fold over all files in this directory and subjacent
     * directories.
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
        return $this->recurseOn()->foldFiles($start_value, $fold_with);
    }

    /**
     * Get an recursor over the content of this directory.
     *
     * @return Recursor
     */
    public function recurseOn() {
        return new Recursor($this);
    }


    // Maybe remove these? Certainly reimplement them...

    /**
     * Get an iterator over the content of this directory.
     *
     * @return DirectoryIterator
     */
    public function iterateOn() {
        return new RawDirectoryIterator($this);
    }
}
