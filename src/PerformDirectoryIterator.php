<?php

namespace Lechimp\Flightcontrol;

class PerformDirectoryIterator extends DirectoryIterator {
    /**
     * @var \Closure 
     */
    protected $action;

    /**
     * @var DirectoryIterator
     */
    protected $prev;

    public function __construct(\Closure $action, DirectoryIterator $prev) {
        $this->action = $action;
        $this->prev = $prev;
    }

    /**
     * @inheritdoc
     */
    public function onContents(\Closure $action) {
        $local_action = $this->action;
        $this->prev->onContents(function(FSObject $obj) use ($action, $local_action) {
            $local_action($obj);
            $action($obj);
        }); 
    }

    /**
     * @inheritdoc
     */
    public function subjacentDirectory() {
        return $this->prev->underlyingDirectory();
    }
}
