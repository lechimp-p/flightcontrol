<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

/**
 * This represents an FSObject-like object that is required during
 * unfolding a directory structure.
 */
class VirtualFSObject extends FSObject {
    /**
     * @return string
     */
    public function name() {
        return $this->path();
    }

    /**
     * @return string
     */
    public function mimetype() {
        return new \LogicException("Don't use mimetype on VirtualFSObjects.");
    }

    /**
     * @return bool
     */
    public function isFile() {
        return false;
    }
}
