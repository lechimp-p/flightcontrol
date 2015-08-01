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
        $dir = $this->flightcontrol->directory("/root");
        $this->assertEquals("/root", $dir->path());
        $this->assertEquals("root", $dir->name());
    }

    public function test_directory2() {
        $obj = $this->flightcontrol->directory("/root/dir_2/dir_2_1");
        $this->assertEquals("/root/dir_2/dir_2_1", $obj->path());
        $this->assertEquals("dir_2_1", $obj->name());
    }

    public function test_directoryNaming() {
        $obj = $this->flightcontrol->directory("/root/dir_2/");
        $this->assertEquals("/root/dir_2", $obj->path());
        $this->assertEquals("dir_2", $obj->name());
    }

    public function test_contents() {
        $dir = $this->flightcontrol->directory("/root");
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
        $dir = $this->flightcontrol->directory("/root/dir_2");
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

    public function test_filterContents() {
        $dir = $this->flightcontrol->directory("/root/dir_2");
        $contents = $dir
            ->filter(function (\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name() == "dir_2_1";
            })
            ->contents();

        $contents = array_map(function($obj) { return $obj->name(); }, $contents);
        $this->assertEquals(array("dir_2_1"), $contents);
    }

    public function test_filterContents2() {
        $dir = $this->flightcontrol->directory("/root/dir_2");
        $contents = $dir
            ->filter(function (\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name() == "dir_2_1";
            })
            ->filter(function (\Lechimp\Flightcontrol\FSObject $obj) {
                return false;
            })
            ->contents();
        $this->assertEquals(array(), $contents);
    }

    public function test_isFile() {
        $dir = $this->flightcontrol->get("/root");
        $this->assertFalse($dir->isFile());
    }
}
