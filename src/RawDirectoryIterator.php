<?php

namespace Lechimp\Flightcontrol;

/**
* An iterator over the contents of a directory.
*/
class RawDirectoryIterator extends DirectoryIterator {
    /**
     * @var     Directory
     */
    protected $dir;

    /**
     * Initialize an iterator over the directory.
     *
     * @param   Directory   $dir
     */
    public function __construct(Directory $dir)  {
        $this->dir = $dir;
    }

    /**
     * No op, as there won't be any action to be performed.
     *
     * @return  null
     */
    public function run() {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function onContents(\Closure $action) {
        foreach($this->dir->contents() as $content) {
            $action($content);
        }
    }

    /**
     * @inheritdoc
     */
    public function subjacentDirectory() {
        return $this->dir;
    }
}
