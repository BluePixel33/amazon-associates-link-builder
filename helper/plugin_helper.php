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
namespace AmazonAssociatesLinkBuilder\helper;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Cron_Constants;

/**
 * Helper class for commonly used functions in the plugin.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Plugin_Helper {

    /**
     * Build key for storing rendered template in cache.
     *
     * @since 1.0.0
     *
     * @param string $asins       List of hyphen separated asins.
     * @param string $marketplace Marketplace of the asin to look into.
     * @param string $store       The identifier of the store to be used for current adunit
     * @param string $template    Template to render the display unit.
     *
     * @return string Template cache key.
     */
    public function build_display_unit_cache_key( $asins, $marketplace, $store, $template ) {
        return 'aalb' . '-' . $asins . '-' . $marketplace . '-' . $store . '-' . $template;
    }

    /**
     * Build key for storing product information in cache.
     *
     * @since 1.0.0
     *
     * @param string $asins       List of hyphen separated asins.
     * @param string $marketplace Marketplace of the asin to look into.
     * @param string $store       The identifier of the store to be used for current adunit
     *
     * @return string Products information cache key.
     */
    public function build_products_cache_key( $asins, $marketplace, $store ) {
        return 'aalb' . '-' . $asins . '-' . $marketplace . '-' . $store;
    }

    /**
     * Clears the cache for the given template name
     *
     * @since 1.0.0
     *
     * @param string $template The template to clear the cache for
     */
    public function clear_cache_for_template( $template ) {
        $this->clear_cache_for_substring( $template );
    }

    /**
     * Clear the cache for keys which contain the given substring
     *
     * @since 1.0.0
     *
     * @param string $substring The substring which is a part of the keys to be cleared
     */
    public function clear_cache_for_substring( $substring ) {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $statement = 'DELETE from ' . $table_prefix . 'options
        WHERE option_name like %s or option_name like %s';
        $transient_timeout_cache = '_transient_timeout_aalb%' . $substring . '%';
        $transient_cache = '_transient_aalb%' . $substring . '%';
        $prepared_statement = $wpdb->prepare( $statement, $transient_timeout_cache, $transient_cache );

        try {
            $wpdb->query( $prepared_statement );
        } catch ( \Exception $e ) {
            error_log( 'Unable to clear cache. Query to clear cache for substring ' . $substring . ' failed with the Exception ' . $e->getMessage() );
        }
    }

    /**
     * Clear the dead expired transients from cache at intervals
     *
     * @since 1.0.0
     */
    public function clear_expired_transients_at_intervals() {
        $randomNumber = rand( 1, 50 );
        // Clear the expired transients approximately once in 50 requests.
        if ( $randomNumber == 25 ) {
            $this->clear_expired_transients();
        }
    }

    /**
     * Clear the dead expired transients from cache
     *
     * @since 1.0.0
     */
    public function clear_expired_transients() {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $transients_prefix = esc_sql( "_transient_timeout_aalb%" );
        $sql = $wpdb->prepare( '
        SELECT option_name
        FROM ' . $table_prefix . 'options
        WHERE option_name LIKE %s
      ', $transients_prefix );
        $transients = $wpdb->get_col( $sql );
        foreach ( $transients as $transient ) {
            // Strip away the WordPress prefix in order to arrive at the transient key.
            $key = str_replace( '_transient_timeout_', '', $transient );
            delete_transient( $key );
        }
        wp_cache_flush();
    }

    /**
     * Displays error messages in preview mode only
     *
     * @since 1.0.0
     *
     * @param string $error_message Error message to be displayed
     */
    public function show_error_in_preview( $error_message ) {
        if ( is_preview() ) {
            //If it's preview mode
            echo '<div class="aalb-preview-message">' . $error_message . '</div>';
        }
    }

    /**
     * Fetches the Wordpress version number
     *
     * @since 1.0.0
     * @return string Version number of Wordpress
     */
    function get_wordpress_version() {
        global $wp_version;

        return $wp_version;
    }

    /**
     * Gets the template uploads dir URL.
     *
     * @since 1.3.2
     * @return String full URL of the template uploads directory
     */
    public function get_template_upload_directory_url() {
        $upload_dir = wp_upload_dir();

        return $upload_dir['baseurl'] . '/' . AALB_TEMPLATE_UPLOADS_FOLDER;
    }

    /**
     * Reads both the templates/ and the uploads/ directory and updates the template list.
     * Helper to replicate the current status of the default and custom templates
     *
     * @since 1.3
     */
    public function refresh_template_list() {
        global $wp_filesystem;
        $this->aalb_initialize_wp_filesystem_api();

        $aalb_templates = array();
        $upload_dir = $this->get_template_upload_directory();

        //Read and update templates from the plugin's template/ directory (Default Templates)
        if ( $handle = opendir( AALB_TEMPLATE_DIR ) ) {
            while ( false !== ( $entry = readdir( $handle ) ) ) {
                $file_name = $this->aalb_get_file_name( $entry );
                $file_extension = $this->aalb_get_file_extension( $entry );
                if ( $file_extension == "css" and file_exists( AALB_TEMPLATE_DIR . $file_name . '.mustache' ) ) {
                    $aalb_templates[] = $file_name;
                }
            }
            closedir( $handle );
        }

        //Read and update templates from the uploads/ directory (Custom Templates)
        if ( $handle = opendir( $upload_dir ) ) {
            while ( false !== ( $entry = readdir( $handle ) ) ) {
                $file_name = $this->aalb_get_file_name( $entry );
                $file_extension = $this->aalb_get_file_extension( $entry );
                if ( $file_extension == "css" and file_exists( $upload_dir . $file_name . '.mustache' ) ) {
                    $aalb_templates[] = $file_name;
                }
            }
        }
        update_option( Db_Constants::TEMPLATE_NAMES, $aalb_templates );
    }

    /**
     * Loads necessary files and initializes WP Filesystem API
     *
     * @since 1.3
     */
    public function aalb_initialize_wp_filesystem_api() {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        WP_Filesystem();
    }

    /**
     * Fetches the Uploads Directory where custom templates are stored.
     * If the dir doesn't exists, it is created and returned.
     *
     * @since 1.3
     * @return string Full directory path of the template uploads directory
     */
    public function get_template_upload_directory() {
        global $wp_filesystem;
        $this->aalb_initialize_wp_filesystem_api();
        $template_upload_path = $this->aalb_get_template_upload_path();
        if ( ! $wp_filesystem->is_dir( $template_upload_path ) && ! $this->aalb_create_dir( $template_upload_path ) ) {
            return false;
        }

        return $template_upload_path;
    }

    /**
     * Gets the template uploads dir name.
     *
     * @since 1.3
     * @return \String path of the template uploads directory
     */
    private function aalb_get_template_upload_path() {
        return $this->aalb_get_uploads_dir_path() . AALB_TEMPLATE_UPLOADS_FOLDER;
    }

    /**
     * Gets the uploads dir name of AALB plugin.
     *
     * @since 1.4.6
     * @return String full path of the uploads directory of AALB
     */
    public function aalb_get_uploads_dir_path() {
        global $wp_filesystem;
        $upload_dir = wp_upload_dir();

        //TODO: Reason for not using directly use $upload_dir['basedir'] instead of calling find_folder
        return $wp_filesystem->find_folder( $upload_dir['basedir'] );
    }

    /**
     * Creates the Directory
     *
     * @param $dir_path  path of directory
     *
     * @since 1.4.6
     * @return boolean true on successful creation of the dir; false otherwise
     */
    public function aalb_create_dir( $dir_path ) {
        if ( ! wp_mkdir_p( $dir_path ) ) {
            error_log( "Error Creating Dir " . $dir_path . ". Please set the folder permissions correctly." );

            return false;
        }

        return true;
    }

    /**
     * Gets the name of the file without the extension
     *
     * @since 1.0
     *
     * @param string $file_name Name of the file
     *
     * @return string  Name of the file without the extension
     */
    function aalb_get_file_name( $file_name ) {
        return substr( $file_name, 0, strlen( $file_name ) - strlen( strrchr( $file_name, '.' ) ) );
    }

    /**
     * Gets the extension of the file
     *
     * @since 1.0
     *
     * @param string $file_name Name of the file
     *
     * @return string  Extension of the file
     */
    public function aalb_get_file_extension( $file_name ) {
        return substr( strrchr( $file_name, '.' ), 1 );
    }

    /**
     * Intialize the db_keys on every update if they are not initialized
     *
     * @since 1.0.0
     */
    public function initialize_db_keys() {
        $initial_db_config = $this->get_initial_db_keys_config();
        foreach ( $initial_db_config as $db_key => $initial_value ) {
            $this->init_option_if_empty( $db_key, $initial_value );
        }
    }

    /**
     * Get db keys config as key-value pairs set initially
     *
     * @since 1.5.3
     */
    private function get_initial_db_keys_config() {
        $upload_dir = wp_upload_dir();

        return array(
            Db_Constants::CUSTOM_UPLOAD_PATH                            => $upload_dir['basedir'] . '/' . Plugin_Constants::UPLOADS_FOLDER,
            Db_Constants::AWS_ACCESS_KEY                                => '',
            Db_Constants::AWS_SECRET_KEY                                => '',
            Db_Constants::STORE_IDS                                     => '',
            Db_Constants::MAXMIND_DB_LAST_UPLOAD_PATH                   => get_option( Db_Constants::CUSTOM_UPLOAD_PATH ),
            Db_Constants::GEOLITE_DB_DOWNLOAD_RETRY_ON_FAILURE_DURATION => AALB_GEOLITE_DB_DOWNLOAD_RETRY_DURATION_MIN,
            Db_Constants::GEOLITE_DB_DOWNLOAD_FAILED_ATTEMPTS           => 0,
            Db_Constants::MARKETPLACE_NAMES                             => array(),
            Db_Constants::CRON_UPDATE_INTERVAL                          => Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_DEFAULT_VALUE
        );
    }

    /**
     * Initialize db option with the value provided
     *
     * @param String db_key  Database key
     * @param String db_value  Database Value
     *
     * @since 1.5.0
     */
    private function init_option_if_empty( $dbkey, $dbvalue ) {
        if ( ! get_option( $dbkey ) ) {
            update_option( $dbkey, $dbvalue );
        }
    }

}

?>
