<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator on a directory.
*/
class DirectoryIterator extends Iterator {
    /**
     * @var FixedFDirectory a
     */
    protected $directory;

    public function __construct(FixedFDirectory $directory) {
        $this->directory = $directory;
    }   

    /**
     * Iterate on the contents of the this iterator.
     *
     * @return Iterator 
     */
    public function iterateOn() {
        return new SubIterator($this);
    }

    /**
     * Get an iterator for every content in the current iterator
     * for which the provided predicate returns true.
     *
     * @param  \Closure             $predicate  (a -> Bool)
     * @return Iterator
     */
    public function filter(\Closure $predicate) {
        return new DirectoryIterator($this->directory->filter($predicate));
    }

    /**
     * Map a function over the objects inside the iterator.
     *
     * @param   \Closure    $trans      a -> b
     * @return  Iterator
     */
    public function map(\Closure $trans) {
        return new DirectoryIterator(
            $this->directory->map($trans)
        ); 
    }

    /**
     * Define the function to be iterated with and close this level
     * of iteration.
     * 
     * @param   \Closure    $iteration  a -> File|Directory -> a
     * @return  Iterator|a
     */
    public function fold($start_value, $iteration) {
        return $this->directory->fold($start_value, $iteration)->contents();
    }

    // Helpers
    
    /**
     * Get an recursor over the content of this directory iterator.
     *
     * @return Recursor
     */
/*    public function recurseOn() {
        return new RawRecursor($this);
    }*/

    /**
     * Get an object that can perform a fold operation on all files in this
     * iterator. 
     *
     * @return  Recursor 
     */
/*    public function foldFiles() {
        return new RawRecursor($this);
    }*/
}
