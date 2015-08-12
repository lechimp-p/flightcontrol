<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class FlightcontrolTest extends _TestCaseBase {
    public function test_getExisting() {
        $this->assertNotNull($this->flightcontrol->get("/root"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_1"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_1/file_1_1"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_1/file_1_2"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_2"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_2/dir_2_1"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_2/dir_2_1/file_2_1_1"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_2/dir_2_1/file_2_1_2"));
        $this->assertNotNull($this->flightcontrol->get("/root/dir_2/file_2_1"));
    }

    public function test_nonExisting() {
        $this->assertNull($this->flightcontrol->get("/rootz/"));
        $this->assertNull($this->flightcontrol->get("/root/dir_1/foo_file"));
    } 

    public function test_directory() {
        $this->assertNotNull($this->flightcontrol->directory("/root"));
        $this->assertNotNull($this->flightcontrol->directory("/root/dir_1"));
        $this->assertNull($this->flightcontrol->directory("/root/dir_1/file_1_1"));
        $this->assertNull($this->flightcontrol->directory("/root/dir_1/file_1_2"));
        $this->assertNotNull($this->flightcontrol->directory("/root/dir_2"));
        $this->assertNotNull($this->flightcontrol->directory("/root/dir_2/dir_2_1"));
        $this->assertNull($this->flightcontrol->directory("/root/dir_2/dir_2_1/file_2_1_1"));
        $this->assertNull($this->flightcontrol->directory("/root/dir_2/dir_2_1/file_2_1_2"));
        $this->assertNull($this->flightcontrol->directory("/root/dir_2/file_2_1"));
    }

    public function test_file() {
        $this->assertNull($this->flightcontrol->file("/root"));
        $this->assertNull($this->flightcontrol->file("/root/dir_1"));
        $this->assertNotNull($this->flightcontrol->file("/root/dir_1/file_1_1"));
        $this->assertNotNull($this->flightcontrol->file("/root/dir_1/file_1_2"));
        $this->assertNull($this->flightcontrol->file("/root/dir_2"));
        $this->assertNull($this->flightcontrol->file("/root/dir_2/dir_2_1"));
        $this->assertNotNull($this->flightcontrol->file("/root/dir_2/dir_2_1/file_2_1_1"));
        $this->assertNotNull($this->flightcontrol->file("/root/dir_2/dir_2_1/file_2_1_2"));
        $this->assertNotNull($this->flightcontrol->file("/root/dir_2/file_2_1"));
    }
}
