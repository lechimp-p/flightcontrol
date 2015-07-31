<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator on a directory.
*/
class DirectoryIterator {
    use NamedFilterTrait;

    /**
     * @var Directory|DirectoryIterator
     */
    protected $previous;

    public function __construct($previous) {
        assert(  $previous instanceof Directory
              || $previous instanceof DirectoryIterator
              );
        $this->previous = $previous;
    }   

    /**
     * Get all content included in this iterator.
     *
     * @return FSObject[]
     */
    public function contents() {
        $returns = array();
        $this->onContents(function($obj) use (&$returns) {
            $returns[] = $obj;
        });
        return $returns;
    }

    /**
     * Iterate over the contents of the current iterator.
     *
     * @return DirectoryIterator 
     */
    public function iterateOn() {
        return new DirectoryIterator($this);
    }

    /**
     * Get an iterator for every content in the current iterator
     * for which the provided predicate returns true.
     *
     * @param  \Closure             $predicate  (FSObject -> Bool)
     * @return DirectoryIterator
     */
    public function filter(\Closure $predicate) {
        return new FilterDirectoryIterator($predicate, $this);
    }

    /**
     * Define the function to be iterated with and close this level
     * of iteration.
     * 
     * @param   \Closure    $iteration  File -> ()
     * @return  DirectoryIterator|null
     */
    public function with($iteration) {
    }
    
    /**
     * Close a level of iteration without an iteration function.
     *
     * @return  DirectoryIterator|null
     */
    public function run() {
        $this->with(function($obj) {});
    }

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

    /**
     * With every content in the iterator do some action.
     *
     * @param   \Closure    $action
     * @return  null
     */
//    abstract public function onContents(\Closure $action);

    /**
     * Get the subjacent directory.
     *
     * @return Directory
     */
//    abstract public function subjacentDirectory();
}
