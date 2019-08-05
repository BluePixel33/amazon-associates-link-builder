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

namespace AmazonAssociatesLinkBuilder\admin;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\includes\Remote_Loader;
use AmazonAssociatesLinkBuilder\helper\Paapi_Helper;
use AmazonAssociatesLinkBuilder\helper\Tracking_Api_Helper;
use AmazonAssociatesLinkBuilder\helper\Settings_Page_Migration_Helper;
use AmazonAssociatesLinkBuilder\configuration\Config_Loader;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Library_Endpoints;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;
use AmazonAssociatesLinkBuilder\includes\GB_Block_Manager;
use AmazonAssociatesLinkBuilder\cache\Item_Lookup_Response_Cache;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;

/**
 * The class responsible for handling all the functionalities in the admin area.
 * Enqueues the styles and scripts for post.php and post-new.php.
 * Fetches the marketplace endpoints from external json file.
 * Handles UI in the admin area by providing a meta box and an asin button in the html text editor.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/admin
 */
class Plugin_Admin {

    private $paapi_helper;
    private $remote_loader;
    private $tracking_api_helper;
    private $helper;
    private $migration_helper;
    private $config_loader;
    private $gb_block_manager;
    private $item_lookup_response_cache;

    public function __construct() {
        $this->paapi_helper = new Paapi_Helper();
        $this->remote_loader = new Remote_Loader();
        $this->tracking_api_helper = new Tracking_Api_Helper();
        $this->helper = new Plugin_Helper();
        $this->migration_helper = new Settings_Page_Migration_Helper();
        $this->config_loader = new Config_Loader();
        $this->gb_block_manager = new GB_Block_Manager();
        $this->item_lookup_response_cache = new Item_Lookup_Response_Cache( new Sql_Helper( DB_NAME, Db_Constants::ITEM_LOOKUP_RESPONSE_TABLE_NAME  ) );
    }

    /**
     * Checks if PA-API Credentials are not set
     *
     * @since 1.4.5
     * @return boolean true if PA-API credentials are set
     */
    public function is_paapi_credentials_not_set() {
        return ( get_option( Db_Constants::AWS_ACCESS_KEY ) == '' or get_option( Db_Constants::AWS_SECRET_KEY ) == '' );
    }

    /**
     * Checks if store-ids credentials are not set
     *
     * @since 1.4.12
     *
     * @return boolean true if store-id credentials are set
     */
    public function is_store_id_credentials_not_set() {
        return ( get_option( Db_Constants::STORE_IDS ) == '' );
    }

    /**
     * Enqueue CSS classes
     *
     * @since 1.4.6
     *
     */
    public function aalb_enqueue_styles() {
        wp_enqueue_style( 'jquery_ui_css', Library_Endpoints::JQUERY_UI_CSS );
        wp_enqueue_style( 'aalb_basics_css', AALB_BASICS_CSS, array( 'jquery_ui_css' ), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        wp_enqueue_style( 'aalb_admin_css', AALB_ADMIN_CSS, array( 'jquery_ui_css' ), Plugin_Constants::PLUGIN_CURRENT_VERSION );

        wp_enqueue_style( 'font_awesome_css', Library_Endpoints::FONT_AWESOME_CSS );
        wp_enqueue_style( 'thickbox' );
    }

    /**
     * Enqueue JS files
     *
     * @since 1.4.6
     *
     */
    public function aalb_enqueue_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'handlebars_js', Library_Endpoints::HANDLEBARS_JS );
        wp_enqueue_script( 'aalb_sha2_js', AALB_SHA2_JS, array(), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'aalb_admin_js', AALB_ADMIN_JS, array( 'handlebars_js', 'jquery', 'jquery-ui-tabs', 'aalb_sha2_js' ), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        wp_localize_script( 'aalb_admin_js', 'api_pref', $this->get_paapi_pref() );
        wp_localize_script( 'aalb_admin_js', 'aalb_strings', $this->get_aalb_strings() );
    }

    /**
     * Returns data to be localized in the script.
     * Makes the variable values in PHP to be used in Javascript.
     *
     * @since 1.0.0
     * @return array Data to be localized in the script
     */
    private function get_paapi_pref() {
        return array(
            'max_search_result_items'         => Plugin_Constants::MAX_SEARCH_RESULT_ITEMS,
            'default_marketplace'             => get_option( Db_Constants::DEFAULT_MARKETPLACE, Db_Constants::DEFAULT_MARKETPLACE_NAME ),
            'ajax_url'                        => admin_url( 'admin-ajax.php' ),
            'action'                          => 'get_item_search_result',
            'item_search_nonce'               => wp_create_nonce( 'aalb-item-search-nonce' ),
            'AALB_SHORTCODE_AMAZON_LINK'      => Plugin_Constants::SHORTCODE_AMAZON_LINK,
            'AALB_SHORTCODE_AMAZON_TEXT'      => Plugin_Constants::SHORTCODE_AMAZON_TEXT,
            'IS_PAAPI_CREDENTIALS_NOT_SET'    => $this->is_paapi_credentials_not_set(),
            'IS_STORE_ID_CREDENTIALS_NOT_SET' => $this->is_store_id_credentials_not_set(),
            'WORDPRESS_REQUEST_TIMEOUT'       => Plugin_Constants::WORDPRESS_REQUEST_TIMEOUT_IN_MS,
            'templates_list'                  => json_encode( get_option( Db_Constants::TEMPLATE_NAMES ) ),
            'default_template'                => get_option( Db_Constants::DEFAULT_TEMPLATE, Db_Constants::DEFAULT_TEMPLATE_NAME ),
            'marketplace_store_id_map'        => get_option( Db_Constants::STORE_IDS )
        );
    }

    /**
     * Returns constant strings to be used in aalb_admin.js
     * Makes the variable values in PHP to be used in Javascript.
     *
     * @since 1.4.4
     * @return array Data to be localized in the script
     */
    private function get_aalb_strings() {
        return array(
            "template_asin_error"                  => esc_html__( "Only one product can be selected for this template", 'amazon-associates-link-builder' ),
            "no_asin_selected_error"               => esc_html__( "Please select at least one product for these marketplaces:", 'amazon-associates-link-builder' ),
            "empty_product_search_bar"             => esc_html__( "Please Enter a Product Name ", 'amazon-associates-link-builder' ),
            "short_code_create_failure"            => esc_html__( "Failed to create Text Link shortcode. Editor has some text selected. Only one item can be selected while adding text links", 'amazon-associates-link-builder' ),
            /* translators: %s: Email-id of the support */
            "paapi_request_timeout_error"          => sprintf( esc_html__( "Request timed out. Try again after some time. Please check your network and firewall settings. If the error still persists, write to us at %s.", 'amazon-associates-link-builder' ), Plugin_Constants::SUPPORT_EMAIL_ID ),
            "add_aalb_shortcode"                   => esc_html__( "Add Amazon Associates Link Builder Shortcode", 'amazon-associates-link-builder' ),
            /* translators: %s: URL of settings page */
            "paapi_credentials_not_set"            => sprintf( __( "Please configure your PA-API credentials in the  <a href=%s>Settings Page</a> to use the Link Builder features.", 'amazon-associates-link-builder' ), AALB_SETTINGS_PAGE_URL ),
            /* translators: %s: URL of settings page */
            "store_id_credentials_not_set"         => sprintf( __( "Please configure your Store-Id credentials in the  <a href=%s>Settings Page</a> to use the Link Builder features.", 'amazon-associates-link-builder' ), AALB_SETTINGS_PAGE_URL ),
            "ad_template_label"                    => esc_html__( "Ad Template", 'amazon-associates-link-builder' ),
            "searchbox_placeholder"                => esc_html__( "Enter keyword(s)", 'amazon-associates-link-builder' ),
            "search_button_label"                  => esc_html__( "Search", 'amazon-associates-link-builder' ),
            "associate_id_label"                   => esc_html__( "Tracking IDs", 'amazon-associates-link-builder' ),
            "search_keyword_label"                 => esc_html__( "Search Phrase", 'amazon-associates-link-builder' ),
            "select_associate_id_label"            => esc_html__( "Select Tracking Id", 'amazon-associates-link-builder' ),
            "marketplace_label"                    => esc_html__( "Marketplace", 'amazon-associates-link-builder' ),
            "select_marketplace_label"             => esc_html__( "Select Marketplace", 'amazon-associates-link-builder' ),
            "text_shown_during_search"             => esc_html__( "Searching relevant products from Amazon", 'amazon-associates-link-builder' ),
            "click_to_select_products_label"       => esc_html__( "Click to select product(s) to advertise", 'amazon-associates-link-builder' ),
            "check_more_on_amazon_text"            => esc_html__( "Check more search results on Amazon", 'amazon-associates-link-builder' ),
            "selected_products_list_label"         => esc_html__( "List of Selected Products(Maximum: 10)", 'amazon-associates-link-builder' ),
            "text_shown_during_shortcode_creation" => esc_html__( "Creating shortcode. Please wait....", 'amazon-associates-link-builder' ),
            "add_shortcode_button_label"           => esc_html__( "Add Shortcode", 'amazon-associates-link-builder' ),
            "templates_help_content"               => esc_html__( "To configure templates, go to Associates Link Builder plugin's Templates page", 'amazon-associates-link-builder' ),
            "marketplace_help_content"             => esc_html__( "To configure marketplaces, go to Associates Link Builder plugin's Settings page", 'amazon-associates-link-builder' ),
            "tracking_id_help_content"             => esc_html__( "To configure tracking ids, go to Associates Link Builder plugin's Settings page", 'amazon-associates-link-builder' ),
            "searched_products_box_placeholder"    => esc_html__( "Please select marketplace from above to show products.", 'amazon-associates-link-builder' ),
            "selected_products_box_placeholder"    => esc_html__( "Please select some products from above.", 'amazon-associates-link-builder' ),
            "pop_up_new_tab_label"                 => esc_html__( "Add ProductSet for Country", 'amazon-associates-link-builder' )
        );
    }

    /**
     * Checks if the plugin has been updated and calls required method
     *
     * @since 1.3
     */
    public function check_update() {
        if ( Plugin_Constants::PLUGIN_CURRENT_VERSION !== get_option( Db_Constants::PLUGIN_VERSION ) ) {
            $this->handle_plugin_update();
        }
    }

    /**
     * Block which runs whenever the plugin has been updated.
     * Refreshes the templates
     *
     * @since 1.3
     */
    public function handle_plugin_update() {
        //Clear all transients for price changes to reflect
        $this->helper->clear_cache_for_substring( '' );
        $this->helper->clear_expired_transients();
        $this->helper->initialize_db_keys();

        global $wp_filesystem;
        $this->helper->aalb_initialize_wp_filesystem_api();
        $this->helper->refresh_template_list();
        $this->migration_helper->run_migration_logic();

        // To init item lookup response cache in update.
        $this->item_lookup_response_cache->init();

        update_option( Db_Constants::PLUGIN_VERSION, Plugin_Constants::PLUGIN_CURRENT_VERSION );
    }

    /**
     * Prints Search box to be displayed in Editor where user can type in keywords for search. @see editor_search_box.php
     * This callback is attached with "media_buttons" hook of wordpress. @see Plugin_Manager::add_admin_hooks()
     *
     * @since 1.4.3 Only prints search box displayed in editor.
     * @since 1.0.0 Prints the aalb-admin sidebar search box.
     */
    function admin_display_callback() {
        require( AALB_EDITOR_SEARCH_BOX );
    }

    /**
     * Prints  Popup box of the plugin used to create shortcode. @see meta_box.php
     * This callback is attached with "admin_footer" hook of wordpress. @see Plugin_Manager::add_admin_hooks()
     *
     * @since 1.4.3
     *
     */
    function admin_footer_callback() {
        require_once( AALB_META_BOX_PARTIAL );
    }

    /**
     * Asin button in text editor for putting the shortcode template
     *
     * @since 1.0.0
     */
    function add_quicktags() {
        if ( wp_script_is( 'quicktags' ) ) {
            ?>
            <script type="text/javascript">
                QTags.addButton( 'aalb_asin_button', 'asins', '[amazon_link asins="" template="" marketplace="" link_id=""]', '', '', 'Amazon Link' );
            </script>
            <?php
        }
    }

    /**
     * Supports the ajax request for item search.
     *
     * @since 1.0.0
     */
    public function get_item_search_result() {
        $nonce = $_GET['item_search_nonce'];

        //verify the user making the request.
        if ( ! wp_verify_nonce( $nonce, 'aalb-item-search-nonce' ) ) {
            die( 'Not authorised to make a request' );
        }

        //Only allow users who can edit post to make the request.
        if ( current_user_can( 'edit_posts' ) ) {
            $url = $this->paapi_helper->get_item_search_url( $_GET['keywords'], $_GET['marketplace'] , $_GET['store_id'] );
            try {
                echo $this->remote_loader->load( $url );
            } catch ( \Exception $e ) {
                echo $this->paapi_helper->get_error_message( $e->getMessage() );
            }
        }

        wp_die();
    }

    /**
     * Supports the ajax request for get link id API
     *
     * @since 1.0.0
     */
    public function get_link_code() {

        $shortcode_params_json_string = $_POST['shortcode_params'];
        $shortcode_name = $_POST['shortcode_name'];

        echo $this->tracking_api_helper->get_link_id( $shortcode_name, $shortcode_params_json_string );
        wp_die();
    }

    /**
     * Supports the ajax request for getting template contents for custom templates
     *
     * @since 1.3
     */
    public function get_custom_template_content() {
        global $wp_filesystem;
        $this->helper->aalb_initialize_wp_filesystem_api();
        $base_path = $this->helper->get_template_upload_directory();
        if ( current_user_can( 'edit_posts' ) ) {
            $css_file = $_POST['css'];
            $real_css_file = realpath( $css_file );
            $mustache_file = $_POST['mustache'];
            $real_mustache_file = realpath( $mustache_file );
            if ( $real_css_file === false || $real_mustache_file === false || strpos( $real_css_file, $base_path ) !== 0 || strpos( $real_mustache_file, $base_path ) !== 0 ) {
                //If base path is not a prefix of the realpath, this means that a directry traversal was attempted
                die( esc_html__( "Not authorised to make request template content or Directory Traversal Attempted.", 'amazon-associates-link-builder' ) );
            } else {
                //No vulnerability. Get file contents.
                $css_file_content = $wp_filesystem->get_contents( $css_file );
                $mustache_file_content = $wp_filesystem->get_contents( $mustache_file );

                $response = array( "css" => $css_file_content, "mustache" => $mustache_file_content );
                echo json_encode( $response );
            }
        } else {
            die( esc_html__( 'Not authorised to make request', 'amazon-associates-link-builder' ) );
        }
        wp_die();
    }

    /**
     * Registers GutenBerg editor block of Amazon Associates Link Builder if supported.
     */
    public function register_gb_block_if_supported()
    {
        if ($this->gb_block_manager->is_gb_block_supported()) {
            $this->gb_block_manager->register_gb_block();
        }
    }

    /**
     * Enqueues block editor assets in Gutenberg editor if supported.
     */
    public function enqueue_block_editor_assets_if_supported()
    {
        if ($this->gb_block_manager->is_gb_block_supported()) {
            $this->aalb_enqueue_styles();
            $this->aalb_enqueue_scripts();
        }
    }

    /**
     * Enqueues block assets.
     */
    public function enqueue_block_assets()
    {
        wp_enqueue_script( 'jquery' );
    }

}

?>
