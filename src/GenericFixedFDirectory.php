<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

class GenericFixedFDirectory extends FixedFDirectory {
    /**
     * @var FDirectory
     */
    protected $fdirectory;

    // TODO: This should directly take an FDirectory.
    public function __construct(FDirectory $fdirectory) {
        parent::__construct($fdirectory->flightcontrol, $fdirectory->filesystem, $fdirectory->path);
        $this->contents = $contents;
    }

    public function unfix() {
        return new FDirectory($this, $this->contents);
    }

    public function isFile() {
        return false;
    } 
}

