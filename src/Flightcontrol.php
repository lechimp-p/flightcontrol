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
     * @var bool
     */
    protected $strict_evaluation;

    /**
     * Initialize the flightcontrol over a flysystem.
     *
     * @param   \League\Flysystem\Filesystem    $filesystem
     */
    public function __construct(\League\Flysystem\Filesystem $filesystem, $strict_evaluation = true) {
        assert(is_bool($strict_evaluation));
        $this->strict_evaluation = $strict_evaluation;
        $this->filesystem = $filesystem;
    }

    /**
     * @return \League\Flysystem\Filesystem
     */
    public function filesystem() {
        return $this->filesystem;
    }

    /**
     * Get the topmost directory in the flightcontrol.
     *
     * @return Directory
     */
    public function root() {
        $contents = $this->filesystem->listContents();
        assert(count($contents) == 1);
        assert($contents[0]["type"] == "dir");
        $dir = $this->directory("/".$contents[0]["path"]);
        assert($dir instanceof Directory);
        return $dir;
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
            return new Directory($this, $path);
        }

        try {
            $info = $this->filesystem->getMetadata($path);
            if ($info) {
                if ($info["type"] == "file") {
                    return new File($this, $path);
                }
                return new Directory($this, $path);
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
        return $this->file_or_dir($path, false);
    }

    /**
     * Get a file from the filesystem.
     *
     * @param   string $path
     * @return  File|null
     */
    public function file($path) {
        return $this->file_or_dir($path, true);
    }

    // Helper
    // Get an object from fs that either is as file or a dir.
    private function file_or_dir($path, $is_file) {
        assert(is_bool($is_file));
        $obj = $this->get($path);
        if ($obj !== null && $is_file === $obj->isFile()) {
            return $obj;
        }
        return null;
    }

    /**
     * Create an FDirectory with metadata from some FSObject and some content
     * that could be lazily produced by some function.
     *
     * @param   FSObject    $fs_object
     * @param   \Closure    $contents_lazy
     * @return  FDirectory
     */
    public function newFDirectory(FSObject $fs_object, \Closure $contents_lazy) {
        $fdir = new FDirectory($fs_object, $contents_lazy);
        if ($this->strict_evaluation) {
            $fdir->fcontents();
        }
        return $fdir;
    }
}
