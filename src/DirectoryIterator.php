<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator on a directory.
*/
abstract class DirectoryIterator {
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
     * Get an iterator for every item in the current iterator where the name
     * matches the provided regular expression.
     *
     * The regexp will be embedded as such "%^$regexp$%" before it is passed
     * to preg_match.
     *
     * @param   string              $regexp
     * @return  DirectoryIterator
     */
    public function named($regexp) {
        assert(is_string($regexp));
        $regexp = "%^$regexp$%";
        return $this->filter(function(FSObject $obj) use ($regexp) {
            return preg_match($regexp, $obj->name());
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
     * Get an object that can perform a fold operation on all files in this
     * iterator. 
     *
     * @return  DirectoryRecursor 
     */
    public function foldFiles() {
        return new DirectoryRecursor($this);
    }

    /**
     * With every content in the iterator do some action.
     *
     * @param   \Closure    $action
     * @return  null
     */
    abstract public function onContents(\Closure $action);
}
