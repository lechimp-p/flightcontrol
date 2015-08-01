<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

class GenericFixedFDirectory extends FSObject {
    /**
     * @var mixed[]     - should really be something like any[]
     */
    protected $contents;

    // TODO: This should directly take an FDirectory.
    public function __construct(FSObject $base, array $contents) {
        parent::__construct($base->flightcontrol, $base->filesystem, $base->path);
        $this->contents = $contents;
    }

    public function unfix() {
        return new FDirectory($this, $this->contents);
    }

    public function isFile() {
        return false;
    } 
}

