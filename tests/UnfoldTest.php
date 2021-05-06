<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

use \League\Flysystem\Memory\MemoryAdapter;
use \League\Flysystem\Filesystem;
use \Lechimp\Flightcontrol\Flightcontrol;

class UnfoldTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->flysystem = new Filesystem(new MemoryAdapter());
        $this->flightcontrol = new FlightControl($this->flysystem);
        $this->flysystem->createDir("write");
        $this->flysystem->createDir("no_write/untouched");
    }

    protected function writeFile($dir_name = "write") {
        $write_to = $this->flightcontrol->get("write");
        $write_to
            ->unfold(1)
            ->with(function($layer) use ($dir_name) {
                //content of the write directory
                if ($layer == 1) {
                    return $this->flightcontrol->makeFDirectory($dir_name, array(0)); 
                }
                // the file:
                $this->assertEquals(0, $layer);
                return $this->flightcontrol->makeFile("file", "content");
            });
    }

    protected function listContents($path = null) {
        return array_map(function($info) {
            return $info["basename"];
        }, $this->flysystem->listContents($path));
    }

    public function test_unfoldWritesFile() {
        $this->writeFile();

        $content = $this->flysystem->read("write/file");
        $this->assertEquals("content", $content);
        $this->assertEquals(array("file"), $this->listContents("write"));
    }

    public function test_unfoldLeavesOtherDirsUntouched() {
        $this->writeFile();

        $dirs  = $this->listContents();
        $this->assertCount(2, $dirs);
        $this->assertContains("write", $dirs);
        $this->assertContains("no_write", $dirs);

        $dirs2 = $this->listContents("no_write");
        $this->assertEquals(array("untouched"), $dirs2);
        
        $this->assertEquals(array(), $this->listContents("no_write/untouched"));
    }

    /**
     * @expectedException \LogicException
     */
    public function test_unfoldInEmptyDirsOnly() {
        $no_write_to = $this->flightcontrol->get("no_write");
        $no_write_to->unfold(1);
    }

    public function test_ignoreRootNodeName() {
        $this->writeFile("another_name");

        $dirs  = $this->listContents();
        $this->assertCount(2, $dirs);
        $this->assertContains("write", $dirs);
        $this->assertContains("no_write", $dirs);
    }

    /**
     * @expectedException \LogicException
     */
    public function test_expectsFDirectoryAsRootNode() {
        $write_to = $this->flightcontrol->get("write");
        $write_to
            ->unfold(0)
            ->with(function($layer) {
                return $this->flightcontrol->makeFile("file", "content");
            });
    }

    public function test_multiLayers() {
        $this->layer2 = 0;
        $this->layer1 = 0;
        $this->layer0 = 0;

        $write_to = $this->flightcontrol->get("write");
        $write_to
            ->unfold(2.0)
            ->with(function($layer) {
                //content of the write directory
                if ($layer >= 2) {
                    $this->layer2++;
                    return $this->flightcontrol->makeFDirectory("write", array(1.1,1.2,1.3));
                }
                // contents of subdirectories
                if ($layer >= 1) {
                    $this->layer1++;
                    $dir_name = "dir$layer";
                    return $this->flightcontrol->makeFDirectory($dir_name, array(0)); 
                }
                // the file:
                $this->layer0++;
                $this->assertEquals(0, $layer);
                return $this->flightcontrol->makeFile("file", "content");
            });

        $this->assertEquals(1, $this->layer2);
        $this->assertEquals(3, $this->layer1);
        $this->assertEquals(3, $this->layer0);

        $dirs  = $this->listContents("write");
        $this->assertCount(3, $dirs);
        $this->assertContains("dir1.1", $dirs);
        $this->assertContains("dir1.2", $dirs);
        $this->assertContains("dir1.3", $dirs);

        $this->assertEquals(array("file"), $this->listContents("write/dir1.1"));
        $this->assertEquals(array("file"), $this->listContents("write/dir1.2"));
        $this->assertEquals(array("file"), $this->listContents("write/dir1.3"));
    }
}
