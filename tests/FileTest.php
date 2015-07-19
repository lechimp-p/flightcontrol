<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class FileTest extends _TestCaseBase {
    public function test_file() {
        $obj = $this->flightcontrol->get("/root/dir_1/file_1_1")->toFile();
        $this->assertEquals("/root/dir_1/file_1_1", $obj->path());
        $this->assertEquals("file_1_1", $obj->name());
    }

    public function test_mimetype() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1")->toFile();
        $this->assertEquals("text/plain", $file->mimetype());
    }

    public function test_timestamp() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertTrue(is_int($file->timestamp()));
        $this->assertEquals($this->flysystem->getTimestamp("/root/dir_1/file_1_1"), $file->timestamp());
    }

    public function test_toDirectory() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1")->toFile();
        $this->assertNull($file->toDirectory());
    }

    public function test_toFile() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertNotNull($file->toFile());
        $this->assertInstanceOf("\\Lechimp\\Flightcontrol\\File", $file->toFile());
    }
}
