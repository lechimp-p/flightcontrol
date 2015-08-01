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
        return $this->wrappedMapTopOnNonFiles(function($obj) use ($predicate) {
            return $obj->filter($predicate);
        });
    }

    /**
     * @inheritdoc 
     */
    public function map(\Closure $trans) {
        return $this->wrappedMapTopOnNonFiles(function ($obj) use ($trans) {
            return new GenericFixedFDirectory(
                $obj->unfix()->fmap($trans)
            );
        });
    }

    /**
     * @inheritdoc 
     */
    public function fold($start_value, $iteration) {
        return $this->mapTopOnNonFiles(function($v) use ($start_value, $iteration) {
            return new GenericFixedFDirectory(
                new FDirectory($v, $v->iterateOn()->fold($start_value, $iteration))
            );
        });
    }

    // Helpers

    protected function mapTop(\Closure $trans) {
        return $this->top->map($trans);
    }

    protected function mapTopOnNonFiles(\Closure $trans) {
        return $this->mapTop(function($obj) use ($trans) {
            $file = $obj->toFile();
            if ($file !== null) {
                return $file;
            }
            assert($obj instanceof FixedFDirectory);
            return $trans($obj);
        });
    }

    protected function wrap(Iterator $iter) {
        return new SubIterator($iter);
    }

    protected function wrappedMapTop(\Closure $trans) {
        return $this->wrap($this->mapTop($trans));
    }

    protected function wrappedMapTopOnNonFiles(\Closure $trans) {
        return $this->wrap($this->mapTopOnNonFiles($trans));
    }
}

