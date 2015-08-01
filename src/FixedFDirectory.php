<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

abstract class FixedFDirectory /* a */ extends FSObject {
    /**
     * See documentation of FDirectory.
     * 
     * @return FDirectory a 
     */
    abstract public function unfix();

    /**
     * Get an iterator over the content of this directory.
     *
     * @return Iterator
     */
    public function iterateOn() {
        return new DirectoryIterator($this);
    }

    /**
     * @inheritdoc
     */
    public function isFile() {
        return false;
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
     * Get the the things inside this abstract directory.
     *
     * @return mixed[]  should really be any[]
     */
     public function contents() {
        return $this->unfix()->fcontents();
     } 
}
