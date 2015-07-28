<?php

namespace Lechimp\Flightcontrol;

class WithContentsDirectoryIterator extends DirectoryIterator {
    /**
     * @var DirectoryIterator
     */
    protected $prev;

    public function __construct(DirectoryIterator $prev) {
        $this->prev = $prev;
    }

    /**
     * @inheritdoc
     */
    public function onContents(\Closure $action) {
        $this->prev->onContents(function(FSObject $obj) use ($action) {
            if ($dir = $obj->toDirectory()) {
                $dir->withContents()->onContents(function(FSObject $obj) use ($action) {
                    $action($obj);
                });
            }
        }); 
    }

    /**
     * @inheritdoc
     */
    public function subjacentDirectory() {
        return $this->prev->underlyingDirectory();
    }
}
