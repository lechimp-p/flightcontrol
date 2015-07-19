<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class FSObjectTest extends _TestCaseBase {
    public function test_directory() {
        $obj = $this->flightcontrol->get("/root");
        $this->assertEquals("/root", $obj->path());
        $this->assertEquals("root", $obj->name());
    }

    public function test_file() {
        $obj = $this->flightcontrol->get("/root/dir_1/file_1_1");
        $this->assertEquals("/root/dir_1/file_1_1", $obj->path());
        $this->assertEquals("file_1_1", $obj->name());
    }
}
