<?php
/******************************************************************************
 * An iterator interface over the Leagues flysystem.
 * Copyright (c) 2021, 2015 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under GPLv3. You should have received
 * a copy of the along with the code.
 */

/**
 * Sets up an adapter to the test_fs.zip, a flysystem and a flightcontrol.
 */
class _TestCaseBase extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $adapter = new \League\Flysystem\ZipArchive\ZipArchiveAdapter(__DIR__."/test_fs.zip");
        /* test_fs.zip:
         *  root
         *      dir_1
         *          file_1_1
         *          file_1_2
         *      dir_2
         *          dir_2_1
         *              file_2_1_1
         *              file_2_1_2
         *          file_2_1
         *
         * where every file contains its name as content.
         */
        $this->flysystem = new \League\Flysystem\Filesystem($adapter);
        $this->flightcontrol = new \Lechimp\Flightcontrol\Flightcontrol($this->flysystem);
    }
}
