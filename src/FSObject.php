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
     * @param   string  $path
     */
    public function __construct(Flightcontrol $flightcontrol, $path) {
        assert(is_string($path));
        $this->flightcontrol = $flightcontrol;
        $this->path = self::normalize($path);
    }

    /**
     * @return Flightcontrol
     */
    public function flightcontrol() {
        return $this->flightcontrol;
    }

    /**
     * @return \League\Flysystem\Filesystem
     */
    public function filesystem() {
        return $this->flightcontrol->filesystem();
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
