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
 * Capture similarities for objects that could be filtered for name.
 */
trait NamedFilterTrait {
    /**
     * Get a recursor/iterator filters on the name of the objects it works on.
     *
     * The regexp will be embedded as such "%^$regexp$%" before it is passed
     * to preg_match.
     */
    public function named($regexp) {
        assert(is_string($regexp));
        $regexp = "%^$regexp$%";
        return $this->filter(function(FSObject $obj) use ($regexp) {
            return preg_match($regexp, $obj->name());
        });
    }

    /**
     * Get an iterator for every directory in the current iterator.
     *
     * @return Iterator
     */
    public function directoriesOnly() {
        return $this->filter(function(FSObject $obj) {
            return $obj->toDirectory() !== null;
        });
    }

    /**
     * Get an iterator for every file in the current iterator.
     *
     * @return Iterator
     */
    public function filesOnly() {
        return $this->filter(function(FSObject $obj) {
            return $obj->toFile() !== null;
        });
    }

    /**
     * This should actually return a filtered version of the object.
     * 
     * @param   \Closure    $predicate
     * @return  mixed
     */
    abstract public function filter(\Closure $predicate);
}
