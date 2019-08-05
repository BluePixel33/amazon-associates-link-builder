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

/**
 * Uninstallation Script. This deletes entries from databse and templates uploads folder.
 *
 * @since 1.8.0
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

//Include hthe Plugin Config file
require_once( plugin_dir_path( __FILE__ ) . 'plugin_config.php' );

/**
 * Remove the settings stored by the admin in the database
 *
 * @since 1.8.0
 */
function aalb_remove_settings() {
    try {
        global $wpdb;
        $table_prefix = $wpdb->prefix;
        $statement = 'DELETE from ' . $table_prefix . 'options
          WHERE option_name like %s';
        $settings = "aalb%";
        $prepared_statement = $wpdb->prepare( $statement, $settings );
        $wpdb->query( $prepared_statement );
    } catch ( Exception $e ) {
        error_log( 'Unable to clear settings. Query failed with the Exception ' . $e->getMessage() );
    }
}

function aalb_create_dir( $dir_path ) {
    if ( ! wp_mkdir_p( $dir_path ) ) {
        error_log( "Error Creating Dir " . $dir_path . ". Please set the folder permissions correctly." );

        return false;
    }

    return true;
}

/**
 * Recursively remove the template uploads dir
 *
 * @since 1.8.0
 */
function aalb_remove_uploads_dir() {
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    try {
        WP_Filesystem();
        global $wp_filesystem;

        $upload_dir = wp_upload_dir();
        $upload_dir = $wp_filesystem->find_folder( $upload_dir['basedir'] );

        $template_upload_path = $upload_dir . AALB_TEMPLATE_UPLOADS_FOLDER;

        if ( ! $wp_filesystem->is_dir( $template_upload_path ) && ! aalb_create_dir( $template_upload_path ) ) {
            return false;
        }
        $wp_filesystem->rmdir( $template_upload_path, true );
    } catch (Exception $e) {
        error_log( 'Unable to remove templates uploads directory. Failed with the Exception ' . $e->getMessage() );
    }
}

aalb_remove_settings();
aalb_remove_uploads_dir();