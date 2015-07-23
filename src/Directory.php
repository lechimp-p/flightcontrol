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
     * Get an object that can perform a fold operation on all files in this
     * iterator. 
     *
     * @return  DirectoryRecursor 
     */
    public function foldFiles() {
        return $this->withContents()->foldFiles();
    }
}
