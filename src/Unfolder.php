<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

/**
* This object can unfold a directory structure into an directory.
*/
class Unfolder {
    /**
     * @var FixedFDirectory
     */
    protected $directory;

    /**
     * @var mixed
     */
    protected $start_value;

    public function __construct(FixedFDirectory $directory, $start_value) {
        $this->directory = $directory;
        $this->start_value = $start_value;
    }

    /**
     * Define the function to unfold the directory structure and perform
     * the unfold operation.
     *
     * @param   \Closure    $unfolder  a -> File|FDirectory a -> a 
     * @return  null
     */
    public function with(\Closure $unfolder) {
    }
}
