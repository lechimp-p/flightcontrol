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
 * This class represents a directory in the FS.
 */
class FilteredDirectory extends FixedFDirectory {
    /**
     * @var FixedFDirectory
     */
    protected $previous;

    /**
     * @var \Closure
     */
    protected $predicate;


    public function __construct(FixedFDirectory $previous, \Closure $predicate) {
        parent::__construct($previous->flightcontrol(), $previous->path);
        $this->previous = $previous;
        $this->predicate = $predicate;
    }

    /**
     * @inheritdoc
     */
    public function unfix() {
        return $this->flightcontrol()->newFDirectory($this, function() {
            return array_filter(
                $this->previous->contents(),
                $this->predicate
            );
        });
    }
}
