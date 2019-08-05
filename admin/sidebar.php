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
namespace AmazonAssociatesLinkBuilder\admin\sidebar;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;

/**
 * The class for adding menu and submenu pages on the sidebar.
 * Registers the settings using the Wordpress Settings API for suing in the partials
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/admin/sidebar
 */
class Sidebar {

    /**
     * Adds categories to the menu page
     *
     * @since 1.0.0
     */
    public function register_sidebar_config_page() {
        $plugin_title_string = esc_html__( "Associates Link Builder", 'amazon-associates-link-builder' );
        // Create new top-level menu
        add_menu_page( $plugin_title_string, $plugin_title_string, 'manage_options', 'associates-link-builder-about', array( $this, 'about_callback' ), AALB_ICON );
        /* translators: %s: Name of plugin */
        add_submenu_page( 'associates-link-builder-about', sprintf( esc_html__( "Configure %s About", 'amazon-associates-link-builder' ), $plugin_title_string ), esc_html__( "About", 'amazon-associates-link-builder' ), 'manage_options', 'associates-link-builder-about', array( $this, 'about_callback' ) );
        /* translators: %s: Name of plugin */
        add_submenu_page( 'associates-link-builder-about', sprintf( esc_html__( "Configure %s Settings", 'amazon-associates-link-builder' ), $plugin_title_string ), esc_html__( "Settings", 'amazon-associates-link-builder' ), 'manage_options', 'associates-link-builder-settings', array( $this, 'settings_callback' ) );
        /* translators: %s: Name of plugin */
        add_submenu_page( 'associates-link-builder-about', sprintf( esc_html__( "Configure %s Templates", 'amazon-associates-link-builder' ), $plugin_title_string ), esc_html__( "Templates", 'amazon-associates-link-builder' ), 'manage_options', 'associates-link-builder-templates', array( $this, 'templates_callback' ) );
    }

    /**
     * Registers credentials to the config group
     *
     * @since 1.0.0
     */
    public function register_cred_config_group() {
        // Register Credentials
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::AWS_ACCESS_KEY, array( $this, 'validate_access_key' ) );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::AWS_SECRET_KEY, array( $this, 'validate_secret_key' ) );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::DEFAULT_STORE_ID );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::DEFAULT_MARKETPLACE );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::DEFAULT_TEMPLATE );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::STORE_ID_NAMES );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::STORE_IDS );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::NO_REFERRER_DISABLED );
        register_setting( Db_Constants::CRED_CONFIG_GROUP, Db_Constants::CUSTOM_UPLOAD_PATH );
    }

    /**
     * Load the about page partial
     * Callbacks to load the page makes the url to use the slug, making it clean.
     *
     * @since 1.0.0
     */
    public function about_callback() {
        require_once( AALB_ABOUT_PHP );
    }

    /**
     * Load the settings page partial
     * The page to save the credentials and default settings of the admin.
     *
     * @since 1.0.0
     */
    public function settings_callback() {
        require_once( AALB_CREDENTIALS_PHP );
    }

    /**
     * Load the template page partial
     * The page to make changes to the templates, add new templates or remove existing templates.
     *
     * @since 1.0.0
     */
    public function templates_callback() {
        require_once( AALB_TEMPLATE_PHP );
    }

    /**
     * Sanitize the access key provided by the admin.
     * Encrypt the access key and store in the db.
     *
     * @since 1.0.0
     *
     * @param string $input Access key input by the user.
     */
    public function validate_access_key( $input ) {
        $old_data = get_option( Db_Constants::AWS_ACCESS_KEY );

        return $this->encrypt_keys( $input, $old_data );
    }

    /**
     * Sanitize the secret key provided by the admin.
     * Encrypt the secret key and store in the db.
     *
     * @since 1.0.0
     *
     * @param string $input Secret key input by the user.
     */
    public function validate_secret_key( $input ) {
        $old_data = get_option( Db_Constants::AWS_SECRET_KEY );

        return $this->encrypt_keys( $input, $old_data );
    }

    /**
     * Encrypt the keys provided by the user.
     * If the data already exists in the database, then do not retrieve and print it on the viewer page.
     * Else encrypt the data and store it in the db.
     *
     * @since 1.0.0
     *
     * @param string $input    Key input by the user to encrypt.
     * @param string $old_data The data if already stored in the database.
     */
    private function encrypt_keys( $input, $old_data ) {
        if ( ! isset( $input ) || trim( $input ) === '' ) {
            return $input;
        } elseif ( $input == Plugin_Constants::AWS_SECRET_KEY_MASK ) {
            return $old_data;
        }

        $output = base64_encode( openssl_encrypt( $input, Plugin_Constants::ENCRYPTION_ALGORITHM, Plugin_Constants::ENCRYPTION_KEY, 0, Plugin_Constants::ENCRYPTION_IV ) );

        return $output;
    }

}

?>
