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
    }

    /**
     * Get an iterator for every directory in the current iterator.
     *
     * @return DirectoryIterator
     */
    public function directories() {
    }

    /**
     * Get an iterator for every file in the current iterator.
     *
     * @return DirectoryIterator
     */
    public function files() {
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
     * With every content in the iterator do some thing.
     *
     * @param   \Closure    $action
     * @return  null
     */
    abstract public function onContents(\Closure $action);
}
