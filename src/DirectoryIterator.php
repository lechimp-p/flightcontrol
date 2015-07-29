<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator on a directory.
*/
abstract class DirectoryIterator {
    use NamedFilterTrait;

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
    public function withContents() {
        return new WithContentsDirectoryIterator($this);
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
     * Get an iterator for every directory in the current iterator.
     *
     * @return DirectoryIterator
     */
    public function directoriesOnly() {
        return $this->filter(function(FSObject $obj) {
            return $obj->toDirectory() !== null;
        });
    }

    /**
     * Get an iterator for every file in the current iterator.
     *
     * @return DirectoryIterator
     */
    public function filesOnly() {
        return $this->filter(function(FSObject $obj) {
            return $obj->toFile() !== null;
        });
    }

    /**
     * Feed every item in the current directory to the provided function while
     * iterating.
     *
     * @param   \Closure            $action
     * @return  DirectoryIterator
     */
    public function perform(\Closure $action) {
        return new PerformDirectoryIterator($action, $this);
    }

    /**
     * Actually run the defined iteration.
     *
     * @return  null
     */
    public function run() {
        $this->onContents(function() {});
    }

    /**
     * Get an recursor over the content of this directory iterator.
     *
     * @return DirectoryRecursor
     */
    public function recurseOn() {
        return new RawDirectoryRecursor($this);
    }
    /**
     * Get an object that can perform a fold operation on all files in this
     * iterator. 
     *
     * @return  DirectoryRecursor 
     */
    public function foldFiles() {
        return new RawDirectoryRecursor($this);
    }

    /**
     * With every content in the iterator do some action.
     *
     * @param   \Closure    $action
     * @return  null
     */
    abstract public function onContents(\Closure $action);

    /**
     * Get the subjacent directory.
     *
     * @return Directory
     */
    abstract public function subjacentDirectory();
}
