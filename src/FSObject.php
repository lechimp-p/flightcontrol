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
abstract class FSObject
{
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

    public function __construct(Flightcontrol $flightcontrol, string $path)
    {
        assert(is_string($path));
        $this->flightcontrol = $flightcontrol;
        $this->path = self::normalize($path);
    }

    public function flightcontrol() : Flightcontrol
    {
        return $this->flightcontrol;
    }

    public function filesystem() : \League\Flysystem\Filesystem
    {
        return $this->flightcontrol->filesystem();
    }

    public function path() : string
    {
        return $this->path;
    }

    public function name() : string
    {
        $parts = explode("/", $this->path);
        return end($parts);
    }

    public function mimetype() : ?string
    {
        return $this->filesystem->mimetype($this->path);
    }

    // Helper

    private static function normalize(string $path) : string
    {
        if (substr($path, -1) == "/") {
            $path = substr($path, 0, strlen($path) - 1);
        }
        return $path;
    }

    abstract public function isFile() : bool;
}
