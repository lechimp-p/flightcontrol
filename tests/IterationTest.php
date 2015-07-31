<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class WithContentsTest extends _TestCaseBase {
    public function test_correctContents() {
        $root = $this->flightcontrol->directory("/root");
        $accu = array();
        $root->withContents()
             ->perform(function (\Lechimp\Flightcontrol\FSObject $obj) use(&$accu) {
                    $accu[] = $obj->name();
                })
             ->run();
        $this->assertCount(2, $accu);
        $this->assertContains("dir_1", $accu);
        $this->assertContains("dir_2", $accu);
    }

    public function test_correctContents2() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->withContents()
              ->perform(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
              ->run();
        $this->assertCount(2, $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_correctContents3() {
        $root = $this->flightcontrol->directory("/root");
        $accu = array();
        $root->withContents()
             ->withContents()
             ->perform(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
             ->run();
        $this->assertCount(4, $accu);
        $this->assertContains("file_1_1", $accu);
        $this->assertContains("file_1_2", $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_filterWorks() {
        $root = $this->flightcontrol->directory("/root");
        $accu = array();
        $root->withContents()
             ->filter(function(\Lechimp\Flightcontrol\FSObject $obj) {
                    return $obj->name() == "dir_1";
                })
             ->perform(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
             ->run();
        $this->assertEquals(array("dir_1"), $accu);
    } 

    public function test_filterDirectories() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->withContents()
              ->directoriesOnly()
              ->perform(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
              ->run();
        $this->assertEquals(array("dir_2_1"), $accu);
    }

    public function test_filterFiles() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->withContents()
              ->filesOnly()
              ->perform(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
              ->run();
        $this->assertEquals(array("file_2_1"), $accu);
    }

    public function test_filterNamed() {
        $dir2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir2->withContents()
             ->named("dir.*")
             ->perform(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
             ->run();
        $this->assertEquals(array("dir_2_1"), $accu);

    }

}
