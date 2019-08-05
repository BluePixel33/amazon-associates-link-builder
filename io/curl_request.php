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

use AmazonAssociatesLinkBuilder\exceptions\Unexpected_Network_Response_Exception;
use AmazonAssociatesLinkBuilder\exceptions\Network_Call_Failure_Exception;

/**
 *
 * Wrapper class over PHP curl Request
 *
 * @since      1.5.3
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/io
 */
class Curl_Request {

    /*
     * Get last modified time of a file from a remote url
     *
     * @since 1.5.3
     *
     * @param $url URL of remote file
     *
     * @return string  last_modified_date on success else an Exception
     *
     * @throws Unexpected_Network_Response_Exception if response is not as expeced and contains undefined values
     *
     */
    public function get_last_modified_date_of_remote_file( $url ) {
        $response = wp_remote_head( $url );
        $headers = wp_remote_retrieve_headers( $response );
        if ( ! empty( $headers ) && isset( $headers['last-modified'] ) ) {
            return $headers['last-modified'];
        } else {
            throw new Unexpected_Network_Response_Exception();
        }
    }

    /*
     * Downloads file from a remote url to a temporary files
     *
     * @since 1.5.3
     *
     * @param $url URL of remote file
     *
     * @return string temporray file after downloading from remote url
     *
     * @throws Network_Call_Failure_Exception if execution failed
     *
     */
    public function download_file_to_temporary_file( $url ) {
        $tmp_file = download_url( $url );
        if ( is_wp_error( $tmp_file ) ) {
            throw new Network_Call_Failure_Exception( "WP_ERROR: " . $tmp_file->get_error_message() );
        }

        return $tmp_file;
    }
}