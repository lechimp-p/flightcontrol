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
        $root->iterateOn()
             ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use(&$accu) {
                $accu[] = $obj->name();
             });
        $this->assertCount(2, $accu);
        $this->assertContains("dir_1", $accu);
        $this->assertContains("dir_2", $accu);
    }

    public function test_correctContents2() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->iterateOn()
              ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
              });
        $this->assertCount(2, $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_correctContents3() {
        $root = $this->flightcontrol->directory("/root");
        $accu = array();
        $root
            ->iterateOn()
                ->iterateOn()
                ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
            ->run();
        $this->assertCount(4, $accu);
        $this->assertContains("file_1_1", $accu);
        $this->assertContains("file_1_2", $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_layeredIteration() {
        $root = $this->flightcontrol->directory("/root");
        $accu1 = array();
        $accu2 = array();
        $accu3 = array();
        $root
            ->iterateOn()
                ->iterateOn()
                    ->iterateOn()
                    ->with(function($obj) use (&$accu3) {
                        $accu3[] = $obj->name();
                    })
                ->with(function($obj) use (&$accu2) {
                    $accu2[] = $obj->name();
                })
            ->with(function($obj) use (&$accu1) {
                $accu1[] = $obj->name();
            });
        $this->assertEquals(array("dir_1", "dir_2"), $accu1);
        $this->assertEquals(array("file_1_1", "file_1_2", "dir_2_1", "file_2_1"), $accu2);
        $this->assertEquals(array("file_2_1_1", "file_2_1_2"), $accu3);
    }

    public function test_filterWorks() {
        $root = $this->flightcontrol->directory("/root");
        $accu = array();
        $root
            ->iterateOn()
            ->filter(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name() == "dir_1";
            })
            ->with(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
            });
        $this->assertEquals(array("dir_1"), $accu);
    } 

    public function test_filterDirectories() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->iterateOn()
              ->directoriesOnly()
              ->with(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
              });
        $this->assertEquals(array("dir_2_1"), $accu);
    }

    public function test_filterFiles() {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir_2->iterateOn()
              ->filesOnly()
              ->with(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
              });
        $this->assertEquals(array("file_2_1"), $accu);
    }

    public function test_filterNamed() {
        $dir2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = array();
        $dir2->iterateOn()
             ->named("dir.*")
             ->with(function(\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
             });
        $this->assertEquals(array("dir_2_1"), $accu);
    }

}
