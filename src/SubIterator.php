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
 * An iterator on each of the contents of another iterator.
 */
class SubIterator extends Iterator
{
    /**
     * @var Iterator
     */
    protected $top;

    public function __construct(Iterator $top)
    {
        $this->top = $top;
    }

    /**
     * @inheritdoc
     */
    public function filter(\Closure $predicate) : Iterator
    {
        // As we are working somewhere down the iteration, we have to apply
        // the filter on all non files in the iterator above and return
        // something similar to this.
        return $this->wrappedMapTopOnNonFiles(function ($obj) use ($predicate) {
            return $obj->filter($predicate);
        });
    }

    /**
     * @inheritdoc
     */
    public function map(\Closure $trans) : Iterator
    {
        // As we are working somewhere down the iteration, we have to map
        // the transformation on all non files in the iterator above and return
        // something similar to this.
        return $this->wrappedMapTopOnNonFiles(function ($obj) use ($trans) {
            return $obj->map($trans);
        });
    }

    /**
     * @inheritdoc
     */
    public function fold($start_value, $iteration)
    {
        // As this unwraps one layer of the iteration, we need to apply the fold
        // function to the things in the iterator above and return something
        // similar to the iterator above.
        return $this->mapTopOnNonFiles(function ($v) use ($start_value, $iteration) {
            return $v->fold($start_value, $iteration);
        });
    }

    // Helpers

    protected function mapTop(\Closure $trans)
    {
        return $this->top->map($trans);
    }

    protected function mapTopOnNonFiles(\Closure $trans)
    {
        return $this->mapTop(function ($obj) use ($trans) {
            if ($obj->isFile()) {
                return $obj;
            }
            return $trans($obj);
        });
    }

    protected function wrap(Iterator $iter)
    {
        return new SubIterator($iter);
    }

    protected function wrappedMapTop(\Closure $trans)
    {
        return $this->wrap($this->mapTop($trans));
    }

    protected function wrappedMapTopOnNonFiles(\Closure $trans)
    {
        return $this->wrap($this->mapTopOnNonFiles($trans));
    }
}
