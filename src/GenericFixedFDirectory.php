<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

class GenericFixedFDirectory extends FixedFDirectory
{
    /**
     * @var FDirectory
     */
    protected $fdirectory;

    public function __construct(FDirectory $fdirectory)
    {
        parent::__construct($fdirectory->flightcontrol(), $fdirectory->path());
        $this->fdirectory = $fdirectory;
    }

    public function unfix()
    {
        return $this->fdirectory;
    }

    public function isFile()
    {
        return false;
    }
}
