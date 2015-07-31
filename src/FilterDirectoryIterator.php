<?php

namespace Lechimp\Flightcontrol;

class FilterDirectoryIterator extends DirectoryIterator {
    use FilteredTrait;

    /**
     * @var DirectoryIterator
     */
    protected $previous;

    public function __construct(\Closure $predicate, DirectoryIterator $previous) {
        $this->previous = $previous;
        $this->predicate = $predicate; 
    }

    /**
     * Hmm, kinda duplicate.
     */
    public function unfix() {
        return 
        $this
        ->previous
        ->unfix()
        ->outer_fmap(function($a) { 
            return $this->_filter($a); 
        });
    } 

    /**
     * @inheritdoc
     */
    public function subjacentDirectory() {
        return $this->prev->subjacentDirectory();
    }
}
