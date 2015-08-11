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
 * This class represents a directory in the FS.
 */
class Directory extends FixedFDirectory {
    /**
     * Get the objects inside the directory.
     *
     * @return FSObject[]
     */
    public function contents() {
        $contents = $this->filesystem()->listContents($this->path());
        $returns = array();
        foreach ($contents as $content) {
            $returns[] = $this->flightcontrol->get($content["path"]);
        }
        return $returns;
    }

    /**
     * @inheritdoc
     */
    public function mimetype() {
        return "directory";
    }

    /**
     * @inheritdoc
     */
    public function toDirectory() {
        return $this;
    }

    /**
     * See documentation of FDirectory.
     * 
     * @return FDirectory File
     */
    public function unfix() {
        return $this->flightcontrol()->newFDirectory($this, function() {
            return $this->contents();
        });
    }

    /**
     * Create a directory structure with an anamorphism an insert it in place of
     * this directory.
     *
     * This is for INTERNAL use, use unfold($start_value)->with($unfolder) instead.
     *
     * @param   \Closure  $unfolder
     * @param   mixed     $start_value
     * @return  null
     */
    public function insertByAna(\Closure $unfolder, $start_value) {
        $insert = FixedFDirectory::ana($unfolder, $start_value);

        $inserter = array();
        $inserter[0] = function($path, FixedFDirectory $directory) use (&$inserter) {
            foreach ($directory->contents() as $content) {
                $new_path = $path."/".$content->name();
                if ($content->isFile()) {
                    $this->filesystem()->write($new_path, $content->content());
                }
                else {
                    $this->filesystem()->createDir($new_path);
                    $inserter[0]($new_path, $content->contents());
                }
            } 
        };
        $inserter[0]($this->path(), $insert);
    }

    /**
     * Make a directory when unfolding a directory structure via Directory::unfold.
     *
     * @param   Flightcontrol   $flightcontrol
     * @param   string          $name
     * @param   array           $content
     * @return  FDirectory a
     */
    public static function makeFDirectory(Flightcontrol $flightcontrol, $name, array $content) {
        return new FDirectory( new VirtualFSObject($flightcontrol, $name)
                             , function() use($content) { return $content; }
                             );
    }

    /**
     * Make a file when unfolding a directory structure via Directory::unfold.
     *
     * @param   Flightcontrol   $flightcontrol
     * @param   string  $name
     * @param   string  $content
     * @return  File
     */
    public static function makeFile(Flightcontrol $flightcontrol, $name, $content) {
        return new VirtualFile($flightcontrol, $name, $content);
    }


}
