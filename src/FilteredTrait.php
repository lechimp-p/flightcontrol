<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

trait FilteredTrait {
    /**
     * @var \Closure
     */
    protected $predicate;

    protected function _filter(array $content) {
        return array_filter($content, $this->predicate);
    }
}


