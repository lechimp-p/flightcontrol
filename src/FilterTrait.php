<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

/**
 * Capture similarities for objects that could be filtered for name.
 */
trait FilterTrait
{
    /**
     * Get a recursor/iterator filters on the name of the objects it works on.
     *
     * The regexp will be embedded as such "%^$regexp$%" before it is passed
     * to preg_match.
     */
    public function named(string $regexp)
    {
        $regexp = "%^$regexp$%";
        return $this->filter(function (FSObject $obj) use ($regexp) {
            return preg_match($regexp, $obj->name());
        });
    }

    /**
     * Get an iterator for every directory in the current iterator.
     */
    public function directoriesOnly() : Iterator
    {
        return $this->filter(function (FSObject $obj) {
            return !$obj->isFile();
        });
    }

    /**
     * Get an iterator for every file in the current iterator.
     */
    public function filesOnly() : Iterator
    {
        return $this->filter(function (FSObject $obj) {
            return $obj->isFile();
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
