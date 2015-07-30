<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

/**
 * Only folds files that match a certain predicate.
 */
class FilteredDirectoryRecursor extends DirectoryRecursor {
    use FilteredTrait;

    /**
     * @var DirectoryRecursor
     */
    protected $previous;

    /**
     * @var \Closure
     */
    protected $predicate;

    public function __construct(DirectoryRecursor $previous, \Closure $predicate) {
        // Don't use DirectoryRecursors constructor, as it expects a Directory.
        parent::__construct($previous->directory());
        $this->predicate = $predicate;        
        $this->previous = $previous;        
    }

    // Helpers

    /**
     * Create a copy of this recursor, but on a different path.
     */
    protected function copyOnDirectory(Directory $directory) {
        return new FilteredDirectoryRecursor( $this->previous->copyOnDirectory($directory)
                                            , $this->predicate);
    }

    /**
     * Get the filtered contents from the directory of this recursor.
     */
    protected function contents() {
        return $this->_filter($this->previous->contents());
    }

    /**
     * Get the directory from the subjacent recursor.
     */
    protected function directory() {
        return $this->previous->directory();
    }
}
