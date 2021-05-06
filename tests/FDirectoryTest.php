<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol\Tests;

class FDirectoryTest extends Base {
    public function test_fmap() {
        $root = $this->flightcontrol->directory("/root");
        $f_root = $root->unfix()
            ->fmap(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name();
            });

        $names = $f_root->fcontents();

        $this->assertCount(2, $names);
        $this->assertContains("dir_1", $names);
        $this->assertContains("dir_2", $names);
    }

    public function test_fmap2() {
        $root = $this->flightcontrol->directory("/root/dir_2");
        $f_root = $root->unfix()
            ->fmap(function(\Lechimp\Flightcontrol\FSObject $obj) {
                $this->assertTrue(  $obj instanceof \Lechimp\Flightcontrol\File
                                 || $obj instanceof \Lechimp\Flightcontrol\Directory
                                 );
                return $obj->name();
            });

        $names = $f_root->fcontents();

        $this->assertCount(2, $names);
        $this->assertContains("dir_2_1", $names);
        $this->assertContains("file_2_1", $names);
    }

    public function test_outer_fmap() {
        $root = $this->flightcontrol->directory("/root/dir_2");
        $f_root = $root->unfix()
            ->outer_fmap(function(array $content) {
                return array();
            });
        $fcontents = $f_root->fcontents();
        $this->assertEquals(array(), $fcontents);
    }

    public function test_filter() {
        $root = $this->flightcontrol->directory("/root/dir_2");
        $f_root = $root->unfix()
            ->filter(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->mimetype() == "directory"; 
            })
            ->fmap(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name();
            });
        $fcontents = $f_root->fcontents();
        $this->assertEquals(array("dir_2_1"), $fcontents);
    }

    public function test_fsObjectProps() {
        $obj = $this->flightcontrol->get("/root/dir_2/");
        $this->assertEquals("/root/dir_2", $obj->path());
        $this->assertEquals("dir_2", $obj->name());
        $this->assertFalse($obj->isFile());
        $this->assertEquals("directory", $obj->mimetype());
    }
}
