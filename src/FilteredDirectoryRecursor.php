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
class FilteredDirectoryRecursor extends DirectoryRecursor {
    /**
     * @var DirectoryRecursor
     */
    protected $previous;

    /**
     * @var \Closure
     */
    protected $predicate;

    public function __construct(\Closure $predicate, DirectoryRecursor $previous) {
        parent::__construct($previous->flightcontrol, $previous->filesystem, $previous->path);
        $this->predicate = $predicate;        
        $this->previous = $previous;        
    }

    /**
     * @inheritdoc
     */
    public function unfix() {
        $unfixed_prev = $this->previous->unfix();
        $content = $unfixed_prev->fcontents();
        // Pick out the interesting objects.
        $filtered_content = array_filter($content, $this->predicate);
        // When those are unfixed, the filter has to apply as well on
        // the contents.
        $filtered_content 
            = array_map(function($obj) {
                            $file = $obj->toFile();
                            if ($file !== null) {
                                return $file;
                            }
                            return $obj->recurseOn()
                                       ->filter($this->predicate);
                        }
                        , $filtered_content);

        return new FDirectory($unfixed_prev, $filtered_content);
    }
}
