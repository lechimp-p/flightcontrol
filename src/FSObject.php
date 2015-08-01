<?php

namespace Lechimp\Flightcontrol;

/**
 * Some object on the filesystem.
 */
abstract class FSObject {
    /**
     * @var Flightcontrol 
     */
    protected $flightcontrol;

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param   \Lechimp\Flightcontrol\Flightcontrol    $flightcontrol
     * @param   \League\Flysystem\Filesytem             $filesystem
     * @param   string  $path
     */
    public function __construct(Flightcontrol $flightcontrol, \League\Flysystem\Filesystem $filesystem, $path) {
        assert(is_string($path));
        $this->flightcontrol = $flightcontrol;
        $this->filesystem = $filesystem;
        $this->path = self::normalize($path);
    }

    /**
     * @return  string
     */
    public function path() {
        return $this->path;
    }

    /**
     * @return string
     */
    public function name() {
        $parts = explode("/", $this->path);
        return end($parts);
    }

    /**
     * @return string
     */
    public function mimetype() {
        return $this->filesystem->getMimetype($this->path);
    }

    // Helper

    private static function normalize($path) {
        if (substr($path, -1) == "/") {
            $path = substr($path, 0, strlen($path) - 1);
        }
        return $path;
    }

    /**
     * @return bool
     */
    abstract public function isFile();

}
