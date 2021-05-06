<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol\Tests;

class FSObjectTest extends Base
{
    public function test_directory()
    {
        $obj = $this->flightcontrol->get("/root");
        $this->assertEquals("/root", $obj->path());
        $this->assertEquals("root", $obj->name());
        $this->assertEquals("directory", $obj->mimetype());
        $this->assertFalse($obj->isFile());
    }

    public function test_directory2()
    {
        $obj = $this->flightcontrol->get("/root/dir_2/dir_2_1");
        $this->assertEquals("/root/dir_2/dir_2_1", $obj->path());
        $this->assertEquals("dir_2_1", $obj->name());
        $this->assertEquals("directory", $obj->mimetype());
        $this->assertFalse($obj->isFile());
    }

    public function test_directoryNaming()
    {
        $obj = $this->flightcontrol->get("/root/dir_2/");
        $this->assertEquals("/root/dir_2", $obj->path());
        $this->assertEquals("dir_2", $obj->name());
    }

    public function test_file()
    {
        $obj = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertEquals("/root/dir_1/file_1_1", $obj->path());
        $this->assertEquals("file_1_1", $obj->name());
        $this->assertEquals("text/plain", $obj->mimetype());
        $this->assertTrue($obj->isFile());
    }
}
