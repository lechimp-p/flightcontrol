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
