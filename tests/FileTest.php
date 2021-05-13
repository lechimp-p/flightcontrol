<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol\Tests;

class FileTest extends Base
{
    public function test_file()
    {
        $file = $this->flightcontrol->file("/root/dir_1/file_1_1.txt");
        $this->assertEquals("/root/dir_1/file_1_1.txt", $file->path());
        $this->assertEquals("file_1_1.txt", $file->name());
    }

    public function test_mimetype()
    {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1.txt");
        $this->assertEquals("text/plain", $file->mimetype());
    }

    public function test_timestamp()
    {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1.txt");
        $this->assertTrue(is_int($file->timestamp()));
        $this->assertEquals($this->flysystem->lastModified("/root/dir_1/file_1_1.txt"), $file->timestamp());
    }

    public function test_content()
    {
        $file_1_1 = $this->flightcontrol->get("/root/dir_1/file_1_1.txt");
        $this->assertEquals("file_1_1", $file_1_1->content());
        $file_2_1_1 = $this->flightcontrol->get("/root/dir_2/dir_2_1/file_2_1_1");
        $this->assertEquals("file_2_1_1", $file_2_1_1->content());
    }

    public function test_isFile()
    {
        $file = $this->flightcontrol->get("/root/dir_1/file_1_1.txt");
        $this->assertTrue($file->isFile());
    }
}
