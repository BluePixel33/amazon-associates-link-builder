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

//Not namespaced intentionally for graceful deactivation on non-namespaced supporting PHP version
use AmazonAssociatesLinkBuilder\includes\Plugin_Manager;
use AmazonAssociatesLinkBuilder\includes\Deactivator;
use AmazonAssociatesLinkBuilder\includes\Activator;
use AmazonAssociatesLinkBuilder\includes\Autoloader;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\cache\Item_Lookup_Response_Cache;

/**
 * The class does all the initialisation of the plugin
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Aalb_Initializer {
    /**
     * Initial bindings, autolaoding classes and execution
     *
     * @param String $plugin_base_name
     * @param String $plugin_file_name
     *
     * @since 1.8.0
     */
    function initialize( $plugin_base_name, $plugin_file_name ) {
        $this->autoload();
        add_filter( 'plugin_action_links_' . $plugin_base_name, array( $this, 'add_action_links' ) );
        register_activation_hook( $plugin_file_name, array( new Activator(), 'activate' ) );
        register_deactivation_hook( $plugin_file_name, array( new Deactivator(), 'deactivate' ) );
        $this->execute();
    }

    /**
     * Autoload the files required for the plugin.
     *
     * @since 1.8.0
     */
    function autoload() {
        require_once( AALB_PLUGIN_DIR . 'vendor/autoload.php' );

        //Load the autoloader for plugin files.
        require_once( AALB_AUTOLOADER );
        Autoloader::register();
    }

    /**
     * Execute the plugin
     *
     * @since 1.8.0
     */
    function execute() {
        $plugin_manager = new Plugin_Manager();
        $plugin_manager->execute();
    }

    function add_action_links( $links ) {
        $mylinks = array(
            '<a href="' . admin_url( 'admin.php?page=associates-link-builder-about' ) . '">' . esc_html__( "About", 'amazon-associates-link-builder' ) . '</a>',
            '<a href="' . admin_url( 'admin.php?page=associates-link-builder-settings' ) . '">' . esc_html__( "Settings", 'amazon-associates-link-builder' ) . '</a>',
            '<a href="' . admin_url( 'admin.php?page=associates-link-builder-templates' ) . '">' . esc_html__( "Templates", 'amazon-associates-link-builder' ) . '</a>',
        );

        return array_merge( $links, $mylinks );
    }
}
