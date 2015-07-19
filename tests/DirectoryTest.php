<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class DirectoryTest extends _TestCaseBase {
    public function test_directory() {
        $dir = $this->flightcontrol->get("/root")->toDirectory();
        $this->assertEquals("/root", $dir->path());
        $this->assertEquals("root", $dir->name());
    }

    public function test_directory2() {
        $obj = $this->flightcontrol->get("/root/dir_2/dir_2_1")->toDirectory();
        $this->assertEquals("/root/dir_2/dir_2_1", $obj->path());
        $this->assertEquals("dir_2_1", $obj->name());
    }

    public function test_directoryNaming() {
        $obj = $this->flightcontrol->get("/root/dir_2/")->toDirectory();
        $this->assertEquals("/root/dir_2", $obj->path());
        $this->assertEquals("dir_2", $obj->name());
    }

    public function test_contents() {
        $dir = $this->flightcontrol->get("/root")->toDirectory();
        $contents = $dir->contents();
        $this->assertCount(2, $contents);
        foreach ($contents as $content) {
            $this->assertInstanceOf("\\Lechimp\\Flightcontrol\\Directory", $content);
            if ($content->name() != "dir_1") {
                $this->assertEquals("dir_2", $content->name());
            }
            else {
                $this->assertEquals("dir_1", $content->name());
            }
        }
    }

    public function test_contents2() {
        $dir = $this->flightcontrol->get("/root/dir_2")->toDirectory();
        $contents = $dir->contents();
        $this->assertCount(2, $contents);
        foreach ($contents as $content) {
            if ($content->name() != "dir_2_1") {
                $this->assertInstanceOf("\\Lechimp\\Flightcontrol\\File", $content);
                $this->assertEquals("file_2_1", $content->name());
            }
            else {
                $this->assertInstanceOf("\\Lechimp\\Flightcontrol\\Directory", $content);
                $this->assertEquals("dir_2_1", $content->name());
            }
        }
    }

    public function test_toFile() {
        $dir = $this->flightcontrol->get("/root");
        $this->assertNull($dir->toFile());
    }

    public function test_toDirectory() {
        $dir = $this->flightcontrol->get("/root");
        $this->assertNotNull($dir->toDirectory());
        $this->assertInstanceOf("\\Lechimp\\Flightcontrol\\Directory", $dir->toDirectory());
    }
}
