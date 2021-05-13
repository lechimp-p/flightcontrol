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
* An iterator on a directory.
*/
class DirectoryIterator extends Iterator
{
    /**
     * @var FixedFDirectory a
     */
    protected $directory;

    public function __construct(FixedFDirectory $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Iterate on the contents of the this iterator.
     */
    public function iterateOn() : Iterator
    {
        return new SubIterator($this);
    }

    /**
     * Get an iterator for every content in the current iterator
     * for which the provided predicate returns true.
     *
     * @param  \Closure             $predicate  (a -> Bool)
     */
    public function filter(\Closure $predicate) : Iterator
    {
        return new DirectoryIterator($this->directory->filter($predicate));
    }

    /**
     * Map a function over the objects inside the iterator.
     *
     * @param   \Closure    $trans      a -> b
     */
    public function map(\Closure $trans) : Iterator
    {
        return new DirectoryIterator(
            $this->directory->map($trans)
        );
    }

    /**
     * Define the function to be iterated with and close this level
     * of iteration.
     *
     * @param   \Closure    $iteration  a -> File|Directory -> a
     * @return  mixed
     */
    public function fold($start_value, $iteration)
    {
        return $this->directory->fold($start_value, $iteration)->contents();
    }
}
