<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

namespace Lechimp\Flightcontrol\Tests;

class RecursionLazyTest extends Base {
    public function setUp() : void {
        parent::setUp();
        $this->flightcontrol = new \Lechimp\Flightcontrol\Flightcontrol($this->flysystem, false);
    }

    public function test_allFiles() {
        $root = $this->flightcontrol->directory("/root");
        $all_files = array_map( function($obj) { return $obj->name();}
                              , $root->recurseOn()->allFiles()
                              );
        $this->assertCount(5, $all_files);
        $this->assertContains("file_1_1", $all_files);
        $this->assertContains("file_1_2", $all_files);
        $this->assertContains("file_2_1_1", $all_files);
        $this->assertContains("file_2_1_2", $all_files);
        $this->assertContains("file_2_1", $all_files);
    }

    public function test_filteredFiles() {
        $root = $this->flightcontrol->directory("/root");
        $all_files = $root
            ->recurseOn()
            ->filter(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return substr($obj->name(), -1) != "1"
                    || !$obj->isFile()
                    ;
            })
            ->allFiles();
        $all_files = array_map( function($obj) { return $obj->name();}
                              , $all_files 
                              );
        $this->assertCount(2, $all_files);
        $this->assertContains("file_1_2", $all_files);
        $this->assertContains("file_2_1_2", $all_files);
    }

    public function test_foldFiles() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->foldFiles(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
                return $accu;
            });

        $this->assertCount(5, $result);
        $this->assertContains("file_1_1", $result);
        $this->assertContains("file_1_2", $result);
        $this->assertContains("file_2_1_1", $result);
        $this->assertContains("file_2_1_2", $result);
        $this->assertContains("file_2_1", $result);
    }

    public function test_filteredFold() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->recurseOn()
            ->filter(function(\Lechimp\Flightcontrol\FSObject $obj) {
                return substr($obj->name(), -1) != "1"
                    || !$obj->isFile()
                    ;
            })
            ->foldFiles(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
                return $accu;
            });

        $this->assertCount(2, $result);
        $this->assertContains("file_1_2", $result);
        $this->assertContains("file_2_1_2", $result);
    }

    public function test_nameFilteredFold() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->recurseOn()
            ->named(".*_1")
            ->foldFiles(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
                return $accu;
            });

        $this->assertCount(1, $result);
        $this->assertContains("file_1_1", $result);
    }

/*    public function test_iteratorHasRecursor() {
        $root = $this->flightcontrol->directory("/root");
        $recursor = $root->withContents()->foldFiles();
        $this->assertInstanceOf("\Lechimp\Flightcontrol\Recursor", $recursor);
    }

    public function test_filteredIsRecursor() {
        $root = $this->flightcontrol->directory("/root");
        $recursor = $root->foldFiles()->filter(function($a) {});
        $this->assertInstanceOf("\Lechimp\Flightcontrol\Recursor", $recursor);
    }*/

    public function test_cata1() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->cata(function(\Lechimp\Flightcontrol\FSObject $obj) {
                if ($obj->isFile()) {
                    return array( $obj->name() => $obj->name() );
                }
                $merged = call_user_func_array("array_merge", $obj->fcontents());
                return array( $obj->name() => $merged);
            });
        
        $expected = array
            ( "root" => array
                ( "dir_1"   => array
                    ( "file_1_1" => "file_1_1"
                    , "file_1_2" => "file_1_2"
                    )
                , "dir_2" => array
                    ( "dir_2_1" => array
                        ( "file_2_1_1" => "file_2_1_1"
                        , "file_2_1_2" => "file_2_1_2"
                        )
                    , "file_2_1" => "file_2_1"
                    )
                )
            );

        $this->assertEquals($expected, $result);
    }

    public function test_generalRecursion() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->recurseOn()
            ->with(function(\Lechimp\Flightcontrol\FSObject $obj) {
                if ($obj->isFile()) {
                    return array($obj->name() => $obj->name());
                }
                $merged = call_user_func_array("array_merge", $obj->fcontents());
                return array( $obj->name() => $merged);
            });
        
        $expected = array
            ( "root" => array
                ( "dir_1"   => array
                    ( "file_1_1" => "file_1_1"
                    , "file_1_2" => "file_1_2"
                    )
                , "dir_2" => array
                    ( "dir_2_1" => array
                        ( "file_2_1_1" => "file_2_1_1"
                        , "file_2_1_2" => "file_2_1_2"
                        )
                    , "file_2_1" => "file_2_1"
                    )
                )
            );

        $this->assertEquals($expected, $result);
    }

    public function test_filteredRecursion() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->recurseOn()
            ->named("dir.*") 
            ->with(function (\Lechimp\Flightcontrol\FSObject $obj) {
                if ($obj->isFile()) {
                    $this->assertTrue(false);
                }

                $fcontents = $obj->fcontents();
                if (empty($fcontents)) {
                    $merged = $fcontents;
                }
                else {
                    $merged = call_user_func_array("array_merge", $fcontents);
                }
                return array( $obj->name() => $merged);
            });
        
        $expected = array
            ( "root" => array
                ( "dir_1"   => array()
                , "dir_2" => array
                    ( "dir_2_1" => array()
                    )
                )
            );

        $this->assertEquals($expected, $result);
    }
}
