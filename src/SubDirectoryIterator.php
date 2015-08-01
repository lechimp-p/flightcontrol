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
 * An iterator on each of the contents of another iterator.
 */
class SubDirectoryIterator extends DirectoryIterator {
    /**
     * @var DirectoryIterator
     */
    protected $previous;

    public function __construct(DirectoryIterator $previous) {
        $this->previous = $previous;
    }

    protected function depth() {
        return $this->previous->depth() + 1;
    }

    /**
     * @inheritdoc 
     */
    public function fold($start_value, $iteration) {
        return $this->previous->map(function($v) use ($start_value, $iteration) {
            if ($v instanceof Directory) {
                return $v->iterateOn()->fold($start_value, $iteration);
            } 
            return $v;
        });
    }

    /**
     * @inheritdoc 
     */
    public function map(\Closure $trans) {
        return new SubDirectoryIterator(
            $this->previous->map($trans)
        );
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
}

