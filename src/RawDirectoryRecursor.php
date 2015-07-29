<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

class RawDirectoryRecursor extends DirectoryRecursor {
    /**
     * @var DirectoryIterator
     */
    protected $iterator;

    public function __construct(DirectoryIterator $iterator) {
        $dir = $iterator->subjacentDirectory();
        parent::__construct($dir->flightcontrol, $dir->filesystem, $dir->path);
        $this->iterator = $iterator;
    }

    /**
     * @inheritdoc
     */
    public function unfix() {
        return new FDirectory( $this
                             , $this->iterator->contents()
                             );
    }
}
