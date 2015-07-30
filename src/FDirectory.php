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
 * This class represents a directory with any content, not spefically files
 * or other directories.
 *
 * This is used to define and implement recursion. The directory structure 
 * could be viewed as such (in Haskellish notation):
 * 
 * There is a datatype for an abstract directory
 *
 *      data FDirectory a = FDirectory Metadata [a]
 *                        | FFile File
 *
 *
 * where Metadata means path and other filesystem metadata. The F stands
 * for Functor. A file could be described as
 *
 *      data File = File Metadata Content
 *
 * and a real filesystem then is
 *      newtype Fix f = Fix (f (Fix f))
 *      type Directory = Fix FDirectory
 *
 * As a real implementation in PHP the metadata part is captured in the
 * FSObject class. As there are no type parameters in PHP, we define FDirectory
 * and Directory as separate classes, where unfix is implemented on Directory.
 */
class FDirectory extends FSObject {
    /**
     * @var mixed[]     Is really any[], see comment at __construct.
     */
    protected $fcontents; 

    /**
     * As we need the metadata from FSObject, we need one of those. The
     * FDirectory a also has contents.
     *
     * Actually the second param should have a type like any, as we expect
     * a list from things of the same type.
     *
     * @param   FSObject    $fs_object
     * @param   mixed[]     $contents
     */
    public function __construct(FSObject $fs_object, array $contents) {
        //TODO: A thing like lazy content might come in handy...
        parent::__construct($fs_object->flightcontrol, $fs_object->filesystem, $fs_object->path);
        $this->fcontents = $contents;
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
        return new FDirectory($this, array_map($trans, $this->fcontents()));
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
        return $this->fcontents;
    }

    /**
     * @inheritdoc
     */
    public function isFile() {
        return false;
    }
}
