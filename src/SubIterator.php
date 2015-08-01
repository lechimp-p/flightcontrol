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
    public function filter(\Closure $predicate) {
        return new SubIterator(
            $this->top->map(function(FSObject $obj) use ($predicate) {
                $file = $obj->toFile();
                if ($file !== null) {
                    return $file;
                }
                assert($obj instanceof FixedFDirectory);
                return $obj->filter($predicate);
            })
        );
    }

    /**
     * @inheritdoc 
     */
    public function map(\Closure $trans) {
        return new SubIterator(
            $this->top->map(function(FSObject $obj) use ($trans) {
                $file = $obj->toFile();
                if ($file !== null) {
                    return $file;
                }
                assert($obj instanceof FixedFDirectory);
                return new GenericFixedFDirectory(
                    $obj->unfix()->fmap($trans)
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

