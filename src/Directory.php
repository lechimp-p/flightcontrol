<?php

namespace Lechimp\Flightcontrol;

/**
 * This class represents a directory in the FS.
 */
class Directory extends FSObject {
    /**
     * Get the objects inside the directory.
     *
     * @return FSObject[]
     */
    public function contents() {
        $contents = $this->filesystem->listContents($this->path());
        $returns = array();
        foreach ($contents as $content) {
            $returns[] = $this->flightcontrol->get($content["path"]);
        }
        return $returns;
    }

    /**
     * @inheritdoc
     */
    public function toDirectory() {
        return $this;
    }

    /**
     * Get an iterator over the content of this directory.
     *
     * @return DirectoryIterator
     */
    public function withContents() {
        return new RawDirectoryIterator($this);
    }
}
