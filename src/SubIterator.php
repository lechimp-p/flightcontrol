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
 * An iterator on each of the contents of another iterator.
 */
class SubIterator extends Iterator {
    /**
     * @var Iterator
     */
    protected $top;

    public function __construct(Iterator $top) {
        $this->top= $top;
    }

    /**
     * @inheritdoc 
     */
    public function contents() {
        die("NYI: contents");
    }

    /**
     * @inheritdoc 
     */
    public function filter(\Closure $predicate) {
        die("NYI: filter");
    }

    /**
     * @inheritdoc 
     */
    public function map(\Closure $trans) {
        return new SubIterator(
            $this->top->map(function(FixedFDirectory $dir) use ($trans) {
                return new GenericFixedFDirectory(
                    $dir->unfix()->fmap($trans)
                );
            })
        );
    }

    /**
     * @inheritdoc 
     */
    public function fold($start_value, $iteration) {
        return $this->top->map(function($v) use ($start_value, $iteration) {
            if ($v instanceof FixedFDirectory) {
                return new GenericFixedFDirectory(
                    new FDirectory($v, $v->iterateOn()->fold($start_value, $iteration))
                );
            } 
            assert($v instanceof File);
            return $v;
        });
    }
}

