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
        return $this->filesystem()->lastModified($this->path);
    }

    public function mimetype() : ?string
    {
        try {
            return $this->filesystem()->mimetype($this->path);
        } catch (\League\Flysystem\UnableToRetrieveMetadata $e) {
            return null;
        }
    }

    public function content() : string
    {
        return $this->filesystem()->read($this->path);
    }

    /**
     * @inheritdoc
     */
    public function isFile() : bool
    {
        return true;
    }
}
