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
 * Only folds files that match a certain predicate.
 */
class FilterDirectoryRecursor {
    /**
     * @var \Closure
     */
    protected $predicate;

    public function __construct(\Closure $predicate, DirectoryRecursor $previous) {
        $this->predicate = $predicate;        
        $this->previous = $previous;        
    }

    /**
     * @inheritdoc
     */
    public function with($init, \Closure $collapse) {
        $predicate = $this->predicate;
        return $this->previous->with($init, function($accu, File $file) use ($predicate, $collapse){
            if ($predicate($file)) {
                return $collapse($accu, $file);
            }
            return $accu;
        });
    }
}
