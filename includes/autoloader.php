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
namespace AmazonAssociatesLinkBuilder\includes;
/**
 * The class reponsible for auto-loading files.
 *
 * Loads the class with respect to their respective directories.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Autoloader {

    private $dir;

    /**
     * Register the autoloader for a directory in the plugin.
     *
     * @since 1.0.0
     *
     * @param string $dir Path of the directory.
     */
    public function __construct( $dir = '' ) {
        if ( ! empty( $dir ) ) {
            $this->dir = $dir;
        }

        spl_autoload_register( array( $this, 'autoload' ) );
    }

    /**
     * Make instances of the autoloaders for each directory in the plugin.
     *
     * @since 1.0.0
     */
    public static function register() {
        new self( AALB_INCLUDES_DIR );
        new self( AALB_ADMIN_DIR );
        new self( AALB_SIDEBAR_DIR );
        new self( AALB_PAAPI_DIR );
        new self( AALB_SHORTCODE_DIR );
        new self( AALB_LIBRARY_DIR );
        new self( AALB_SIDEBAR_HELPER_DIR );
        new self( AALB_IP_2_COUNTRY_DIR );
        new self( AALB_EXCEPTIONS_DIR );
        new self( AALB_IO_DIR );
        new self( AALB_HELPER_DIR );
        new self( AALB_CONFIGURATION_DIR );
        new self( AALB_RENDERING_DIR );
        new self( AALB_CACHE_DIR );
        new self( AALB_SQL_DIR );
        new self( AALB_CONSTANTS_DIR );
        new self( AALB_CRON_DIR );
    }

    /**
     * Callback function of spl_autoload_register to autoload the class.
     *
     * @since 1.0.0
     *
     * @param string $class Name of the class to autoload.
     */
    public function autoload( $class ) {
        $class = $this->get_non_namespaced_class_name( $class );
        $path = $this->dir . strtolower( $class ) . '.php';
        if ( file_exists( $path ) ) {
            require_once( $path );
        }
    }

    /** Remove the namespace from the class name as the file_name does not contain namespace.
     *
     * @since 1.8.0
     *
     * @param string $class Name of the class.
     */
    private function get_non_namespaced_class_name( $class ) {
        $pos = strrpos( $class, '\\' );

        return $pos === false ? $class : substr( $class, $pos + 1 );

    }
}

?>
