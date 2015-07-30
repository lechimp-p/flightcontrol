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
     * Get an iterator over the content of this directory.
     *
     * @return DirectoryIterator
     */
    public function withContents() {
        return new RawDirectoryIterator($this);
    }

    /**
     * Get an recursor over the content of this directory.
     *
     * @return DirectoryRecursor
     */
    public function recurseOn() {
        return $this->withContents()->recurseOn();
    }

    /**
     * Get an object that can perform a fold operation on all files in this
     * iterator. 
     *
     * @return  DirectoryRecursor 
     */
    public function foldFiles() {
        return $this->withContents()->foldFiles();
    }

    /**
     * See documentation of FDirectory.
     * 
     * @return FDirectory
     */
    public function unfix() {
        return $this->recurseOn()->unfix();
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
}
