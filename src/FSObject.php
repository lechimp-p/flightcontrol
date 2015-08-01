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

    /**
     * Perform one function or the other, dependening on whether this is
     * a file or not.
     *
     * @param   \Closure    $on_file
     * @param   \Closure    $on_directory
     * @return  mixed
     */ 
    public function patternMatch(\Closure $on_file, \Closure $on_directory) {
        if ($this->isFile()) {
            return $on_file($this);
        }
        else {
            return $on_directory($this); 
        }
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
