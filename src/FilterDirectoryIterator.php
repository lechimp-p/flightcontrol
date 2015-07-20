<?php

namespace Lechimp\Flightcontrol;

class FilterDirectoryIterator extends DirectoryIterator {
    /**
     * @var \Closure
     */
    protected $predicate;

    /**
     * @var DirectoryIterator
     */
    protected $prev;

    public function __construct(\Closure $predicate, DirectoryIterator $prev) {
        $this->prev = $prev;
        $this->predicate = $predicate; 
    }

    /**
     * @inheritdoc
     */
    public function onContents(\Closure $action) {
        $predicate = $this->predicate;
        $this->prev->onContents(function(FSObject $obj) use ($predicate, $action) {
            if ($predicate($obj)) {
                $action($obj);
            }
        }); 
    }
}
