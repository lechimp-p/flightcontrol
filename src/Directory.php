<?php

namespace Lechimp\Flightcontrol;

/**
 * This class represents a directory in the FS.
 */
class Directory extends FSObject {
    /**
     * @inheritdoc
     */
    public function toDirectory() {
        return $this;
    }
}
