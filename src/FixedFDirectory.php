<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol;

abstract class FixedFDirectory /* a */ extends FSObject
{
    /**
     * See documentation of FDirectory.
     *
     * @return FDirectory<a>
     */
    abstract public function unfix() : FDirectory;

    /**
     * Get an iterator over the content of this directory.
     */
    public function iterateOn() : Iterator
    {
        return new DirectoryIterator($this);
    }

    /**
     * Get an recursor over the content of this directory.
     */
    public function recurseOn() : Recursor
    {
        return new Recursor($this);
    }

    /**
     * Get an object that can unfold a directory structure in this directory.
     *
     * @param   mixed   $start_value
     * @throws  \LogicException          If the directory is not empty.
     */
    public function unfold($start_value) : Unfolder
    {
        if (count($this->contents()) > 0) {
            throw new \LogicException("Can't unfold into non-empty directory '" . $this->path() . "'.");
        }
        return new Unfolder($this, $start_value);
    }

    /**
     * @inheritdoc
     */
    public function isFile() : bool
    {
        return false;
    }

    /**
     * Only regard contents that match the predicate.
     *
     * @param   \Closure    $predicate  File|Directory -> bool
     */
    public function filter(\Closure $predicate) : FixedFDirectory
    {
        return new GenericFixedFDirectory(
            $this->unfix()->filter($predicate)
        );
    }

    /**
     * Map over the contents of this directory.
     *
     * @param   \Closure    $trans
     */
    public function map(\Closure $trans) : FixedFDirectory
    {
        return new GenericFixedFDirectory(
            $this->unfix()->fmap($trans)
        );
    }

    /**
     * Map the contents this directory.
     *
     * @param   \Closure    $trans
     */
    public function outer_map(\Closure $trans) : Directory
    {
        return new GenericFixedFDirectory(
            $this->unfix()->outer_fmap($trans)
        );
    }

    /**
     * Fold the contents of this directory with a function.
     *
     * Provide a start value that is fed together with the any content
     * of this directory to the function successively to get a new value.
     *
     * @param   \Closure    $iteration  a -> b -> a
     */
    public function fold($start_value, $iteration) : FixedFDirectory
    {
        return new GenericFixedFDirectory(
            $this->unfix()->fold($start_value, $iteration)
        );
    }

    /**
     * Get the the things inside this abstract directory.
     *
     * @return mixed
     */
    public function contents()
    {
        return $this->unfix()->fcontents();
    }

    /**
     * We could also use the catamorphism on this to do recursion, as we
     * have an unfix and an underlying fmap from the FDirectory.
     *
     * Supply a function $trans from File|FDirectory a to a that flattens
     * (folds) a directory. Will start the directories where only files are
     * included, folds them and then proceeds upwards.
     *
     * The return type should be 'a' (from the function $trans) instead
     * of mixed, but we can't express that fact correctly in the docstring
     * typing.
     *
     * @param   \Closure    $trans      File|FDirectory a -> a
     * @return  mixed
     */
    public function cata(\Closure $trans)
    {
        return $this->recurseOn()->cata($trans);
    }

    /**
     * Fold over all files in this directory and subjacent
     * directories.
     *
     * Start with an initial value of some type and a function from that type
     * and File to a new value of the type. Will successively feed all files
     * and the resulting values to that function.
     *
     * @param   mixed       $start_value
     * @param   \Closure    $fold_with      a -> File -> a
     * @return Recursor|array
     */
    public function foldFiles($start_value, \Closure $fold_with)
    {
        return $this->recurseOn()->foldFiles($start_value, $fold_with);
    }

    /**
     * As we have an unfix and an underlying fmap from FDirectory, we could
     * also implement the anamorphism.
     *
     * An anamorphism creates a structure from a start value and thus somehow
     * is the reverse of cata.
     *
     * You need to provide a function from a to File or FDirectory a. This
     * function then is recursively applied on the contents it produces,
     * starting with the start value.
     *
     * @param   \Closure    $unfolder   a -> File|FDirectory a
     * @param   mixed       $start_value
     */
    final public static function ana(\Closure $unfolder, $start_value) : FSObject
    {
        $unfolded = $unfolder($start_value);
        if ($unfolded->isFile()) {
            return $unfolded;
        }

        return new GenericFixedFDirectory(
            $unfolded->fmap(function ($value) use ($unfolder) {
                return self::ana($unfolder, $value);
            })
        );
    }
}
