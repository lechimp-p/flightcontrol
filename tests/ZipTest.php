<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class ZipTest extends _TestCaseBase {
    public function test_zipStructure() {
        $this->assertCount(2, $this->flysystem->listContents("/root/"));
        $this->assertCount(2, $this->flysystem->listContents("/root/dir_1"));
        $this->assertCount(2, $this->flysystem->listContents("/root/dir_2"));
        $this->assertCount(2, $this->flysystem->listContents("/root/dir_2/dir_2_1"));
        $this->assertEmpty($this->flysystem->listContents("/root/dir_3"));
        $this->assertTrue($this->flysystem->has("/root/dir_1/file_1_1"));
        $this->assertTrue($this->flysystem->has("/root/dir_1/file_1_2"));
        $this->assertTrue($this->flysystem->has("/root/dir_2/file_2_1"));
        $this->assertTrue($this->flysystem->has("/root/dir_2/dir_2_1/file_2_1_1"));
        $this->assertTrue($this->flysystem->has("/root/dir_2/dir_2_1/file_2_1_2"));
    }
}
