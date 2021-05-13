<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol\Tests;

class IterationLazyTest extends Base
{
    public function setUp() : void
    {
        parent::setUp();
        $this->flightcontrol = new \Lechimp\Flightcontrol\Flightcontrol($this->flysystem, false);
    }

    public function test_correctContents()
    {
        $root = $this->flightcontrol->directory("/root");
        $accu = [];
        $root->iterateOn()
             ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                 $accu[] = $obj->name();
             });
        $this->assertCount(2, $accu);
        $this->assertContains("dir_1", $accu);
        $this->assertContains("dir_2", $accu);
    }

    public function test_correctContents2()
    {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = [];
        $dir_2->iterateOn()
              ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                  $accu[] = $obj->name();
              });
        $this->assertCount(2, $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_correctContents3()
    {
        $root = $this->flightcontrol->directory("/root");
        $accu = [];
        $root
            ->iterateOn()
                ->iterateOn()
                ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                    $accu[] = $obj->name();
                })
            ->run();
        $this->assertCount(4, $accu);
        $this->assertContains("file_1_1.txt", $accu);
        $this->assertContains("file_1_2.txt", $accu);
        $this->assertContains("dir_2_1", $accu);
        $this->assertContains("file_2_1", $accu);
    }

    public function test_layeredIteration()
    {
        $root = $this->flightcontrol->directory("/root");
        $accu1 = [];
        $accu2 = [];
        $accu3 = [];
        $in1 = [false];
        $in2 = [false];
        $in3 = [false];
        $root
            ->iterateOn()
                ->iterateOn()
                    ->iterateOn()
                    ->with(function ($obj) use (&$accu3, &$in3) {
                        $in3[0] = true;
                        $accu3[] = $obj->name();
                    })
                ->with(function ($obj) use (&$accu2, &$in2) {
                    $in2[0] = true;
                    $accu2[] = $obj->name();
                })
            ->with(function ($obj) use (&$accu1, &$in1) {
                $in1[0] = true;
                $accu1[] = $obj->name();
            });
        $this->assertTrue($in1[0]);
        $this->assertTrue($in2[0]);
        $this->assertTrue($in3[0]);
        $this->assertEquals(["dir_1", "dir_2"], $accu1);
        $this->assertEquals(["file_1_1.txt", "file_1_2.txt", "dir_2_1", "file_2_1"], $accu2);
        $this->assertEquals(["file_2_1_1", "file_2_1_2"], $accu3);
    }

    public function test_filteredLayeredIteration()
    {
        $root = $this->flightcontrol->directory("/root");
        $accu1 = [];
        $accu2 = [];
        $accu3 = [];
        $root
            ->iterateOn()
            ->filter(function ($obj) {
                return substr($obj->name(), -1) == "1";
            })
                ->iterateOn()
                ->filter(function ($obj) {
                    return substr($obj->name(), -5, 1) == "1";
                })
                    ->iterateOn()
                    ->filter(function ($obj) {
                        return substr($obj->name(), -1) == "1";
                    })
                    ->with(function ($obj) use (&$accu3) {
                        $accu3[] = $obj->name();
                    })
                ->with(function ($obj) use (&$accu2) {
                    $accu2[] = $obj->name();
                })
            ->with(function ($obj) use (&$accu1) {
                $accu1[] = $obj->name();
            });
        $this->assertEquals(["dir_1"], $accu1);
        $this->assertEquals(["file_1_1.txt"], $accu2);
        $this->assertEquals([], $accu3);
    }

    public function test_filterWorks()
    {
        $root = $this->flightcontrol->directory("/root");
        $accu = [];
        $root
            ->iterateOn()
            ->filter(function (\Lechimp\Flightcontrol\FSObject $obj) {
                return $obj->name() == "dir_1";
            })
            ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                $accu[] = $obj->name();
            });
        $this->assertEquals(["dir_1"], $accu);
    }

    public function test_filterDirectories()
    {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = [];
        $dir_2->iterateOn()
              ->directoriesOnly()
              ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                  $accu[] = $obj->name();
              });
        $this->assertEquals(["dir_2_1"], $accu);
    }

    public function test_filterFiles()
    {
        $dir_2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = [];
        $dir_2->iterateOn()
              ->filesOnly()
              ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                  $accu[] = $obj->name();
              });
        $this->assertEquals(["file_2_1"], $accu);
    }

    public function test_filterNamed()
    {
        $dir2 = $this->flightcontrol->directory("/root/dir_2");
        $accu = [];
        $dir2->iterateOn()
             ->named("dir.*")
             ->with(function (\Lechimp\Flightcontrol\FSObject $obj) use (&$accu) {
                 $accu[] = $obj->name();
             });
        $this->assertEquals(["dir_2_1"], $accu);
    }
}
