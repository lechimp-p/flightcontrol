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
 * A file.
 */
class File extends FSObject
{
    /**
     * @return string|int
     */
    public function timestamp()
    {
        return $this->filesystem()->getTimestamp($this->path);
    }

    /**
     * @return string
     */
    public function mimetype()
    {
        return $this->filesystem()->getMimetype($this->path);
    }

    /**
     * @return
     */
    public function content()
    {
        return $this->filesystem()->read($this->path);
    }

    /**
     * @inheritdoc
     */
    public function isFile()
    {
        return true;
    }
}
