<?php

namespace Lechimp\Flightcontrol;

class FilterIterator extends Iterator {
    use FilteredTrait;

    /**
     * @var Iterator
     */
    protected $previous;

    public function __construct(\Closure $predicate, Iterator $previous) {
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

    protected function depth() {
        return $this->previous->depth();
    }
}
