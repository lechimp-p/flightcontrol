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
 * This represents a File-like object that is required during
 * unfolding a directory structure.
 */
class VirtualFile extends VirtualFSObject
{
    /**
     * @var string
     */
    protected $content;

    public function __construct(Flightcontrol $flightcontrol, $path, $content)
    {
        parent::__construct($flightcontrol, $path);
        assert(is_string($content));
        $this->content = $content;
    }

    public function isFile() : bool
    {
        return true;
    }

    public function content() : string
    {
        return $this->content;
    }
}
