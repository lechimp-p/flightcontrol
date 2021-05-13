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
 * The flightcontrol serves an interface over the Leagues flysystem.
 *
 * The interface consists of objects for files and directories means
 * to iterate over the directories.
 */
class Flightcontrol
{
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var bool
     */
    protected $strict_evaluation;

    /**
     * Initialize the flightcontrol over a flysystem.
     */
    public function __construct(
        \League\Flysystem\Filesystem $filesystem,
        bool $strict_evaluation = true
    ) {
        assert(is_bool($strict_evaluation));
        $this->strict_evaluation = $strict_evaluation;
        $this->filesystem = $filesystem;
    }

    public function filesystem() : \League\Flysystem\Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Get an object from the filesystem based on its path.
     */
    public function get(string $path) : FSObject
    {
        // TODO: This does not deal with ~ for home directory.

        if ($this->filesystem->fileExists($path)) {
            return new File($this, $path);
        }

        return new Directory($this, $path);
    }

    /**
     * Get a directory from the filesystem.
     *
     * Dependening on the adapter in the underlying flysystem, this might treat
     * empty directories as if they would not exist (e.g. for ZipArchiveAdapter).
     */
    public function directory(string $path) : ?Directory
    {
        return $this->file_or_dir($path, false);
    }

    /**
     * Get a file from the filesystem.
     */
    public function file(string $path) : ?File
    {
        return $this->file_or_dir($path, true);
    }

    /**
     * Make a directory when unfolding a directory structure via Directory::unfold.
     */
    public function makeFDirectory(string $name, array $content) : FDirectory
    {
        return Directory::makeFDirectory($this, $name, $content);
    }

    /**
     * Make a file when unfolding a directory structure via Directory::unfold.
     */
    public function makeFile(string $name, string $content) : VirtualFile
    {
        return Directory::makeFile($this, $name, $content);
    }

    // Helper

    // Get an object from fs that either is as file or a dir.
    private function file_or_dir(string $path, bool $is_file) : ?FSObject
    {
        $obj = $this->get($path);
        if ($obj !== null && $is_file === $obj->isFile()) {
            return $obj;
        }
        return null;
    }

    /**
     * Create an FDirectory with metadata from some FSObject and some content
     * that could be lazily produced by some function.
     */
    public function newFDirectory(FSObject $fs_object, \Closure $contents_lazy) : FDirectory
    {
        $fdir = new FDirectory($fs_object, $contents_lazy);
        if ($this->strict_evaluation) {
            $fdir->fcontents();
        }
        return $fdir;
    }
}
