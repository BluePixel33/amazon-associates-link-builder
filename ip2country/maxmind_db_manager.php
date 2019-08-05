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

namespace AmazonAssociatesLinkBuilder\ip2Country;

use AmazonAssociatesLinkBuilder\constants\Library_Endpoints;
use AmazonAssociatesLinkBuilder\io\Curl_Request;
use AmazonAssociatesLinkBuilder\io\File_System_Helper;
use AmazonAssociatesLinkBuilder\exceptions\Unexpected_Network_Response_Exception;
use AmazonAssociatesLinkBuilder\exceptions\Network_Call_Failure_Exception;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;

/**
 *
 * Manages the operations related to maxmind GeoLite2Country database & maintains regular updates for the same
 *
 * @since      1.5.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/ip2country
 */
class Maxmind_Db_Manager {
    private $db_upload_dir;
    private $db_file_path;
    private $curl_request_obj;
    private $file_system_helper;

    public function __construct( $db_upload_dir, Curl_Request $curl_request_obj, File_System_Helper $file_system_helper ) {
        $this->curl_request_obj = $curl_request_obj;
        $this->file_system_helper = $file_system_helper;
        $this->db_upload_dir = $db_upload_dir;
        $this->db_file_path = $this->db_upload_dir . Plugin_Constants::MAXMIND_DATA_FILENAME;
        clearstatcache( true, $this->db_file_path );
    }

    /*
     * Returns the error message if any in updating maxmind database. On success it returns an empty string
     *
     * @return String Error_message if any else empty string if no error
     *
     * @since 1.5.3
     */
    public function get_error_message() {
        $error_msg = "";
        if ( ! file_exists( $this->db_file_path ) ) {
            if ( ! is_writable( $this->db_upload_dir ) ) {
                $error_msg = sprintf( esc_html__( "WordPress does not have permissions to write to the \"Downloads Folder\" (%s). Please grant permissions or pick a different folder from the Amazon Associates Link Builder plugin's settings page, otherwise your links to Amazon might not display correctly.", 'amazon-associates-link-builder' ), $this->db_upload_dir );
            } else {
                $error_msg = sprintf( __( "WordPress could not find file %1s at \"DownloadsFolder\"(%2s), so geo-targeted links will not work correctly. This file can be downloaded from <a href=%2s>here</a>", 'amazon-associates-link-builder' ), Plugin_Constants::MAXMIND_DATA_FILENAME, $this->db_upload_dir, Library_Endpoints::GEOLITE_DB_DOWNLOAD_URL_FROM_MAXMIND_SERVER );
            }
        } else {
            if ( ! is_readable( $this->db_file_path ) ) {
                $error_msg = sprintf( esc_html__( "WordPress could not read %s. Please grant read permissions, otherwise your links to Amazon might not display correctly.", 'amazon-associates-link-builder' ), $this->db_file_path );
            } else if ( $this->get_file_age( $this->db_file_path ) > AALB_GEOLITE_DB_MAX_ALLOWED_AGE ) {
                if ( ! is_writable( $this->db_file_path ) ) {
                    $error_msg = sprintf( __( "WordPress does not have write permissions to update the file(%s). Please grant write permissions, otherwise geo-targeted links may not work correctly.", 'amazon-associates-link-builder' ), $this->db_file_path );
                } else {
                    $error_msg = sprintf( __( "WordPress could not update file(%1s) for geo-targeted links feature, so these links may not work correctly. This file can be downloaded from <a href=%2s>here</a>", 'amazon-associates-link-builder' ), $this->db_file_path, Library_Endpoints::GEOLITE_DB_DOWNLOAD_URL_FROM_MAXMIND_SERVER );
                }
            }
        }

        return $error_msg;
    }

    /*
     * Finds the age of file
     *
     * @param String $file_path
     *
     * @return int Age of file in seconds
     *
     * @since 1.5.3
     */
    private function get_file_age( $file_path ) {
        return time() - filemtime( $file_path );
    }

    /*
     * It checks if the GeoLite Db downloaded file has expired and call for update
     *
     * @since 1.5.0
     *
     */
    public function update_db_if_required() {
        try {
            if ( $this->is_file_update_permissible() ) {
                $this->reset_db_keys_if_required();
                if ( time() >= get_option( Db_Constants::GEOLITE_DB_DOWNLOAD_NEXT_RETRY_TIME ) ) {
                    if ( $this->should_update_db() ) {
                        $this->update_db();
                    }
                    $this->update_next_retry_time( Plugin_Constants::SUCCESS );

                }
            }
        } catch ( Network_Call_Failure_Exception $e ) {
            $this->action_on_update_db_failure( $e->errorMessage() );
        } catch ( Unexpected_Network_Response_Exception $e ) {
            $this->action_on_update_db_failure( $e->errorMessage() );
        } catch ( \Exception $e ) {
            $this->action_on_update_db_failure( "Unexpected Exception Ocurred" . $e->getMessage() );
        }
    }

    /**
     * Downloads & updates the maxmind db file(GeoLite2 Country)
     *
     * @argument HTTP Response $response
     *
     * @throws Network_Call_Failure_Exception
     *
     * @since    1.5.0
     *
     */
    private function update_db() {
        $tmp_file = $this->curl_request_obj->download_file_to_temporary_file( Library_Endpoints::GEOLITE_COUNTRY_DB_DOWNLOAD_URL );
        $this->file_system_helper->write_a_gzipped_file_to_disk( $this->db_file_path, $tmp_file );
    }


    /**
     * It logs the error message and updates next retry time
     *
     * @param \String Error message
     *
     *
     * @since 1.5.3
     *
     **/
    private function action_on_update_db_failure( $error_msg ) {
        $this->log_error( $error_msg );
        $this->update_next_retry_time( Plugin_Constants::FAIL );
    }

    /*
    * It checks if upload path has changed and in case of change, set lat upload path as new path, next retry time to 0 & reset failure counters
    *
    * @since 1.5.0
    *
    */
    private function reset_db_keys_if_required() {
        if ( $this->is_upload_path_changed() ) {
            update_option( Db_Constants::MAXMIND_DB_LAST_UPLOAD_PATH, $this->db_upload_dir );
            update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_NEXT_RETRY_TIME, 0 );
            $this->reset_failure_counters();
        }
    }

    /*
    * It checks if earlier upload permissions were not present but were given just before running this function
    *
    * @return bool true if upload_permission_given_recently
    *
    * @since 1.5.3
    *
    */
    private function is_file_update_permissible() {
        if ( ! file_exists( $this->db_file_path ) ) {
            return is_writable( $this->db_upload_dir );
        } else {
            return is_readable( $this->db_file_path ) && is_writable( $this->db_file_path );
        }
    }

    /*
    * It checks if upload path for maxmind db has changed
    *
    * @since 1.5.3
    *
    */
    private function is_upload_path_changed() {
        return $this->db_upload_dir !== get_option( Db_Constants::MAXMIND_DB_LAST_UPLOAD_PATH );
    }

    /*
     * It checks if file is not present or if present, a newwer version is vavialble
     *
     * @since 1.5.0
     *
     * @bool True if geolite db should be updated
     */
    private function should_update_db() {
        return ! file_exists( $this->db_file_path ) || ( $this->is_updated_version_available() );
    }

    /*
    * It checks if the current version of GeoLite Db file's last modified date is greater than the time file was written in db
    *
    * @ since 1.5.0
    *
    * @return bool True if geolite db's newer version is available
    */
    private function is_updated_version_available() {
        return strtotime( $this->curl_request_obj->get_last_modified_date_of_remote_file( Library_Endpoints::GEOLITE_COUNTRY_DB_DOWNLOAD_URL ) ) > filemtime( $this->db_file_path );
    }

    /**
     * It updates the next retry time for downloading maxmind
     *
     * @param String status SUCCESS or FAIL
     *
     * @since 1.5.3
     *
     */
    private function update_next_retry_time( $status ) {
        if ( $status == Plugin_Constants::SUCCESS ) {
            update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_NEXT_RETRY_TIME, time() + AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_ON_SUCCESS );
            $this->reset_failure_counters();
        } else {
            update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_NEXT_RETRY_TIME, time() + $this->get_next_retry_duration() );
        }
    }

    /**
     * It returns the next-retry duration & also updates no. of failed attempts & failure_duration
     *
     * @since 1.5.3
     *
     */
    private function get_next_retry_duration() {
        $number_of_failed_attempts = get_option( Db_Constants::GEOLITE_DB_DOWNLOAD_FAILED_ATTEMPTS );
        $new_retry_duration = AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_MIN * pow( 2, $number_of_failed_attempts );
        if ( $new_retry_duration > AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_MAX ) {
            $new_retry_duration = AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_MAX;
        }
        update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_FAILED_ATTEMPTS, $number_of_failed_attempts + 1 );
        update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_RETRY_ON_FAILURE_DURATION, $new_retry_duration );

        return $new_retry_duration;
    }

    /**
     * It resets the failure counters for next retry
     *
     * @since 1.5.3
     *
     */
    private function reset_failure_counters() {
        update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_RETRY_ON_FAILURE_DURATION, AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_MIN );
        update_option( Db_Constants::GEOLITE_DB_DOWNLOAD_FAILED_ATTEMPTS, 0 );
    }

    /*
     * Log error if allowed
     *
     * @param String error_msg
     *
     * @since 1.5.3
     */
    private function log_error( $error_msg ) {
        if ( $this->should_log_error() ) {
            error_log( $error_msg );
        }
    }

    /*
     * Checks if error should be logged or not
     *
     * @return bool true if should_log_error
     *
     * @since 1.5.3
     */
    private function should_log_error() {
        return current_user_can( 'activate_plugins' ) && is_admin();
    }
}

?>