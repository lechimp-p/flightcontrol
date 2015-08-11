<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
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

    protected function writeFile() {
        $write_to = $this->flightcontrol->get("write");
        $write_to
            ->unfold(1)
            ->with(function($layer) {
                //content of the write directory
                if ($layer == 1) {
                    return $this->flightcontrol->makeFDirectory("write", array(0)); 
                }
                // the file:
                $this->assertEquals(0, $layer);
                return $this->flightcontrol->makeFile("file", "content");
            });
    }

    protected function listContents($path = null) {
        return array_map(function($info) {
            return $info["filename"];
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
}