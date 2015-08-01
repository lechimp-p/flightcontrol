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
        $file = $this->flightcontrol->file("/root/dir_1/file_1_1");
        $this->assertEquals("/root/dir_1/file_1_1", $file->path());
        $this->assertEquals("file_1_1", $file->name());
    }

    public function test_mimetype() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertEquals("text/plain", $file->mimetype());
    }

    public function test_timestamp() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertTrue(is_int($file->timestamp()));
        $this->assertEquals($this->flysystem->getTimestamp("/root/dir_1/file_1_1"), $file->timestamp());
    }

    public function test_content() {
        $file_1_1 = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertEquals("file_1_1", $file_1_1->content());
        $file_2_1_1 = $this->flightcontrol->get("/root/dir_2/dir_2_1/file_2_1_1");
        $this->assertEquals("file_2_1_1", $file_2_1_1->content());
    }

    public function test_isFile() {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertTrue($file->isFile());
    }
}
