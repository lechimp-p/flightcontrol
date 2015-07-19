<?php

namespace Lechimp\Flightcontrol;

/**
 * A file.
 */
class File extends FSObject {
    /**
     * @return int
     */
    public function timestamp() {
        return $this->filesystem->getTimestamp($this->path);
    }

    /**
     * @return string
     */
    public function mimetype() {
        return $this->filesystem->getMimetype($this->path);
    }

    /**
     * @return
     */
    public function content() {
        return $this->filesystem->read($this->path);
    }

    /**
     * @inheritdoc
     */
    public function toFile() {
        return $this;
    }
}
