<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class FDirectoryTest extends _TestCaseBase {
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
}
