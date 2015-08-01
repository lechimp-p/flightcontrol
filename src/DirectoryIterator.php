<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator on a directory.
*/
class DirectoryIterator {
    use NamedFilterTrait;

    /**
     * @var Directory   This should be some interface like Fixed really... 
     */
    protected $directory;

    public function __construct(/*Directory*/ $directory) {
        $this->directory = $directory;
    }   

    /**
     * Get all content included in this iterator.
     *
     * @return FSObject[]
     */
    public function contents() {
        $returns = array();
        $this->with(function($obj) use (&$returns) {
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
        return new SubDirectoryIterator($this);
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
     * @param   \Closure    $iteration  a -> File|Directory -> a
     * @return  DirectoryIterator|a
     */
    public function fold($start_value, $iteration) {
        $contents = $this->unfix()->fcontents();
        foreach($contents as $content) {
            $start_value = $iteration($start_value, $content);
        }
        return $start_value;
    }

    /**
     * Like fold, but with no start value or return.
     *
     * @param   \Closure    $iteration  File|Directory -> () 
     * @return  DirectoryIterator|null
     */
    public function with($iteration) {
        return $this->fold(array(), function($a, $f) use ($iteration) { 
                                        $iteration($f); 
                                        $a[] = $f; // Do not disturb the structure of
                                        return $a; // the directory tree.
                                    });
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
     * Hmm duplicate.
     */
    public function unfix() {
        return $this->directory->unfix();
    }

    // Helpers
    
    /**
     * Get the depth of an iterator, that is the level of nested
     * iterations we're in.
     */
    protected function depth() {
        return 1;
    }

    /**
     * Create a copy of this recursor, but on a different path.
     *
     * TODO: I need some real type hint here...
     */
    protected function copyOnFDirectory(FDirectory $directory) {
        $fixed = new GenericFixedFDirectory($this->subjacentDirectory(), $directory->fcontents());
        return new DirectoryIterator($directory);
    }

    /**
     * Get the subjacent directory.
     *
     * @return Directory
     */
    public function subjacentDirectory() {
        return $this->directory;
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
