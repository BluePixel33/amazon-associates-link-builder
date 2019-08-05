<?php
/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/

namespace AmazonAssociatesLinkBuilder\io;
/**
 *
 * I/O operation related to File System
 *
 * @since      1.5.3
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/io
 */

class File_System_Helper {
    /*
         * Write file to disk
         */
    public function write_a_gzipped_file_to_disk( $outFile, $tmp_file ) {
        $current_file = fopen( $outFile, 'w' );
        $donwloaded_file = gzopen( $tmp_file, 'r' );
        while ( ( $string = gzread( $donwloaded_file, 4096 ) ) != false ) {
            fwrite( $current_file, $string, strlen( $string ) );
        }
        gzclose( $donwloaded_file );
        fclose( $current_file );
        unlink( $tmp_file );
    }
}