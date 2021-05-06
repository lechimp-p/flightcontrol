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
 * This class represents a directory with any content, not spefically files
 * or other directories.
 *
 * This is used to define and implement recursion. The directory structure 
 * could be viewed as such (in Haskellish notation):
 * 
 * There is a datatype for an abstract directory
 *
 *      data FDirectory a = FDirectory Metadata a
 *                        | FFile File
 *
 *
 * where Metadata means path and other filesystem metadata. The F stands
 * for Functor. A file could be described as
 *
 *      data File = File Metadata Content
 *
 * and a real filesystem then is
 *      newtype Directory = Directory { unfix :: FDirectory [Directory] }
 *
 * As a real implementation in PHP the metadata part is captured in the
 * FSObject class. As there are no type parameters in PHP, we define FDirectory
 * and Directory as separate classes, where unfix is implemented on FixedFDirectory.
 *
 * The implementation is also a little clunky, as this FDirectory mixes functionality
 * for FDirectory a and FDirectory [a]. That is FDirectory::outer_fmap is the 'real'
 * fmap from the functor FDirectory.
 */
class FDirectory extends FSObject {
    /**
     * @var \Closure
     */
    protected $contents_lazy; 

    /**
     * @var mixed
     */
    protected $contents = null;

    /**
     * As we need the metadata from FSObject, we need one of those. The
     * FDirectory a also has contents.
     *
     * Actually the second param should have a type like any, as we expect
     * a list from things of the same type.
     *
     * @param   FSObject    $fs_object
     * @param   \Closure    $contents_lazy
     */
    public function __construct(FSObject $fs_object, \Closure $contents_lazy) {
        parent::__construct($fs_object->flightcontrol(), $fs_object->path());
        $this->contents_lazy = $contents_lazy;
    }

    /**
     * As this is as functor, we could map over it.
     *
     * Turns an FDirectory a to an FDirectory b by using the provided $trans
     * function.
     *
     * @param   \Closure    $trans   a -> b
     * @return  FDirectory          
     */
    public function fmap(\Closure $trans) {
        return $this->flightcontrol()->newFDirectory($this, function() use ($trans) { 
            return array_map($trans, $this->fcontents());
        });
    }

    /**
     * We could also map the complete array to a new array, e.g. to
     * filter it, make it longer or shorter.
     *
     * @param   \Closure    $trans  [a] -> [b]
     * @throws  UnexpectedValueException    in case $trans returns no error
     * @return  FDirectory
     */
    public function outer_fmap(\Closure $trans) {
        return $this->flightcontrol()->newFDirectory($this, function() use ($trans) {
            return $trans($this->fcontents());
        });
    }

    /**
     * Define the function to be iterated with and close this level
     * of iteration.
     * 
     * @param   \Closure    $iteration  a -> File|Directory -> a
     * @return  Iterator|a
     */
    public function fold($start_value, $iteration) {
        return $this->outer_fmap(function($contents) use ($start_value, $iteration) {
            $value = $start_value;
            foreach($contents as $content) {
                $value = $iteration($value, $content);
            }
            return $value;
        });
    }

    /**
     * We could filter the FDirectory by a $predicate-
     *
     * @param   \Closure    $predicate  a -> bool
     * @return  FDirectory
     */
    public function filter(\Closure $predicate) {
        return 
        $this->outer_fmap(function($fcontents) use ($predicate) {
            return array_filter($fcontents, $predicate); 
        });
    }

    /**
     * The contents of this directory.
     *
     * It should really return type any[], as we do want to return an array
     * from things of the same type (but depend on the construction of this
     * object).
     *
     * @return  mixed[]     for an FDirectory a
     */
    public function fcontents() {
        if ($this->contents === null) {
            $cl = $this->contents_lazy;
            $this->contents = $cl();
        }
        return $this->contents;
    }

    /**
     * @inheritdoc
     */
    public function isFile() {
        return false;
    }
}
