<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2014, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received 
 * a copy of the along with the code.
 */

class RecursionTest extends _TestCaseBase {
    public function test_foldFiles() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->foldFiles()
            ->with(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
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
            ->foldFiles()
            ->filter(function(\Lechimp\Flightcontrol\File $file) {
                return substr($file->name(), -1) != "1";
            })
            ->with(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
            });

        $this->assertCount(2, $result);
        $this->assertContains("file_1_2", $result);
        $this->assertContains("file_2_1_2", $result);
    }

    public function test_nameFilteredFold() {
        $root = $this->flightcontrol->directory("/root");
        $result = $root
            ->foldFiles()
            ->named(".*_1")
            ->with(array(), function($accu, \Lechimp\Flightcontrol\File $file) {
                $accu[] = $file->name(); 
            });

        $this->assertCount(3, $result);
        $this->assertContains("file_1_1", $result);
        $this->assertContains("file_2_1_1", $result);
        $this->assertContains("file_2_1", $result);
    }

    public function test_iteratorHasRecursor() {
        $root = $this->flightcontrol->directory("/root");
        $recursor = $root->withContents()->foldFiles();
        $this->assertInstanceOf("\Lechimp\Flightcontrol\DirectoryRecursor", $recursor);
    }
}
