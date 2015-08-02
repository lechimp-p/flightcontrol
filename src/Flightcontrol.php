<?php

namespace Lechimp\Flightcontrol;

/**
 * The flightcontrol serves an interface over the Leagues flysystem.
 *
 * The interface consists of objects for files and directories means
 * to iterate over the directories.
 */
class Flightcontrol {
    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * Initialize the flightcontrol over a flysystem.
     *
     * @param   \League\Flysystem\Filesystem    $filesystem
     */
    public function __construct(\League\Flysystem\Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }
    
    /**
     * Get an object from the filesystem based on its path.
     *
     * Dependening on the adapter in the underlying flysystem, this might treat
     * empty directories as if they would not exist (e.g. for ZipArchiveAdapter).
     *
     * @param   string  $path
     * @return  FSObject|null
     */
    public function get($path) {
        // TODO: This does not deal with ~ for home directory.

        assert(is_string($path));

        // For ZipArchiveAdapter this is required to get the directories correctly,
        // as Filesystem::get will raise.
        if ($this->filesystem->listContents($path)) {
            return new Directory($this, $this->filesystem, $path);
        }

        try {
            $info = $this->filesystem->getMetadata($path);
            if ($info) {
                if ($info["type"] == "file") {
                    return new File($this, $this->filesystem, $path);
                }
                return new Directory($this, $this->filesystem, $path);
            }
        }
        catch (\League\Flysystem\FileNotFoundException $e) {
            return null;
        }
        return null;
    }

    /**
     * Get a directory from the filesystem.
     *
     * Dependening on the adapter in the underlying flysystem, this might treat
     * empty directories as if they would not exist (e.g. for ZipArchiveAdapter).
     *
     * @param   string $path
     * @return  Directory|null
     */
    public function directory($path) {
        $obj = $this->get($path);
        if ($obj !== null && !$obj->isFile()) {
            return $obj;
        }
        return null;
    }

    /**
     * Get a file from the filesystem.
     *
     * @param   string $path
     * @return  File|null
     */
    public function file($path) {
        $obj = $this->get($path);
        if ($obj !== null && $obj->isFile()) {
            return $obj;
        }
        return null;
    }

    /**
     * Create an FDirectory with metadata from some FSObject and some content
     * that could be lazily produced by some function.
     *
     * @param   FSObject    $fs_object
     * @param   \Closure    $lazy_content
     * @return  FDirectory
     */
    public function newFDirectory(FSObject $fs_obj, \Closure $lazy_content) {
        return new FDirectory($fs_obj, $lazy_contents);
    }
}
