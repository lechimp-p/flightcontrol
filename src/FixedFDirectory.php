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
     * Get an recursor over the content of this directory.
     *
     * @return Recursor
     */
    public function recurseOn() {
        return new Recursor($this);
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
        return new GenericFixedFDirectory(
            $this->unfix()->filter($predicate)
        );
    }

    /**
     * Map over the contents of this directory.
     *
     * @param   \Closure    $trans
     * @return  Directory
     */
    public function map(\Closure $trans) {
        return new GenericFixedFDirectory(
            $this->unfix()->fmap($trans)
        );
    }

    /**
     * Map the contents this directory.
     *
     * @param   \Closure    $trans
     * @return  Directory
     */
    public function outer_map(\Closure $trans) {
        return new GenericFixedFDirectory(
            $this->unfix()->outer_map($trans)
        );
    }

    /**
     * Fold the contents of this directory with a function.
     *
     * Provide a start value that is fed together with the any content
     * of this directory to the function successively to get a new value.
     * 
     * @param   \Closure    $iteration  a -> b -> a
     * @return  FixedFDirectory
     */
    public function fold($start_value, $iteration) {
        return new GenericFixedFDirectory(
            $this->unfix()->fold($start_value, $iteration)
        );
    }

    /**
     * Get the the things inside this abstract directory.
     *
     * @return mixed
     */
     public function contents() {
        return $this->unfix()->fcontents();
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
}
