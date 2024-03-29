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
abstract class Iterator
{
    use FilterTrait;

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
    abstract public function filter(\Closure $predicate) : Iterator;

    /**
     * Map a function over the objects inside the iterator.
     *
     * @param   \Closure    $trans      a -> b
     */
    abstract public function map(\Closure $trans) : Iterator;

    /**
     * Define the function to be iterated with and close this level
     * of iteration.
     *
     * @param   \Closure    $iteration  a -> File|Directory -> a
     * @return  Iterator|mixed
     */
    abstract public function fold($start_value, $iteration);

    /**
     * Like fold, but with no start value or return.
     *
     * @param   \Closure    $iteration  File|Directory -> ()
     * @return  Iterator|mixed
     */
    public function with($iteration)
    {
        return $this
        ->fold(
            array(),
            function ($a, $f) use ($iteration) {
                $iteration($f);
                // As the subjacent FDirectory is lazy, we need to evaluate
                // the contents, as there might be additional computations
                // hidden.
                if (!$f->isFile()) {
                    $f->contents();
                }
                $a[] = $f; // Do not disturb the structure of
                return $a; // the directory tree.
            }
        );
    }
    
    /**
     * Close a level of iteration without an iteration function.
     *
     * @return  Iterator|mixed
     */
    public function run()
    {
        $this->with(function ($obj) {
        });
    }
}
