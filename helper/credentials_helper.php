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

use AmazonAssociatesLinkBuilder\configuration\Config_Loader;
use AmazonAssociatesLinkBuilder\constants\Library_Endpoints;
use AmazonAssociatesLinkBuilder\ip2Country\Maxmind_Db_Manager;
use AmazonAssociatesLinkBuilder\io\Curl_Request;
use AmazonAssociatesLinkBuilder\io\File_System_Helper;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;

/**
 * Helper class for commonly used functions in the credentials page of plugin.
 *
 * @since      1.4.12
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/admin/sidebar/partials/helper
 */
class Credentials_Helper {

    /**
     * Returns data to be localized in the script.
     * Makes the variable values in PHP to be used in Javascript.
     *
     * @since 1.4.12
     * @return array Data to be localized in the script
     */
    private function credentials_data() {
        return array(
            'old_store_id_db_key'        => Db_Constants::STORE_ID_NAMES,
            'new_store_id_db_key'        => Db_Constants::STORE_IDS,
            'new_store_ids'              => get_option( Db_Constants::STORE_IDS ),
            'default_marketplace_db_key' => Db_Constants::DEFAULT_MARKETPLACE,
            'default_marketplace_value'  => get_option( Db_Constants::DEFAULT_MARKETPLACE ),
            'marketplace_list'           => $this->get_marketplace_list(),
            'default_store_id_db_key'    => Db_Constants::DEFAULT_STORE_ID

        );
    }

    /**
     * Returns constant strings to be used in aalb_credentials.js
     * Makes the variable values in PHP to be used in Javascript.
     *
     * @since 1.4.12
     * @return array Data to be localized in the script
     */
    private function credentials_strings() {
        //ToDO: Make default marketplace and remove marketplace also as label and put all labels together
        return array(
            'tracking_id_placeholder'           => esc_html__( "Enter Tracking Id(s)", 'amazon-associates-link-builder' ),
            'remove_marketplace_label'          => esc_html__( "Remove Marketplace", 'amazon-associates-link-builder' ),
            'select_marketplace_label'          => esc_html__( "Select Marketplace", 'amazon-associates-link-builder' ),
            'default_marketplace_label'         => esc_html__( "Default Marketplace", 'amazon-associates-link-builder' ),
            'set_as_default_marketplace_label'  => esc_html__( "Set As Default Marketplace", 'amazon-associates-link-builder' ),
            "tracking_id_fieldset_label"        => esc_html__( "Tracking Id(s)", 'amazon-associates-link-builder' ),
            "add_a_marketplace_label"           => esc_html__( "Add a Marketplace", 'amazon-associates-link-builder' ),
            'remove_marketplace_confirmation'   => esc_html__( "Remove Marketplace Confirmation", 'amazon-associates-link-builder' ),
            "empty_store_id_error"              => esc_html__( "ERROR: No store id has been entered for one or more marketplaces.", 'amazon-associates-link-builder' ),
            "marketplace_exists_error"          => esc_html__( "ERROR: A marketplace already exists with this value. Please set a new marketplace.", 'amazon-associates-link-builder' ),
            "marketplace_not_set_error"         => esc_html__( "ERROR: A marketplace is present that has not been set. Please set that first.", 'amazon-associates-link-builder' ),
            "remove_last_marketplace_error"     => esc_html__( "ERROR: You need to maintain at least one marketplace entry for tracking ids ", 'amazon-associates-link-builder' ),
            "no_marketplace_row_error"          => esc_html__( "ERROR: You need to add at least one marketplace entry for tracking ids ", 'amazon-associates-link-builder' ),
            "marketplace_list_empty_error"      => esc_html__( "ERROR: No marketplace is available for selection. This may be due to blocked call by your hosting provider or firewall settings to fetch marketplace details from webservices.amazon.com", 'amazon-associates-link-builder' ),
            "marketplace_settings_info_message" => esc_html__( "Add a Marketplace that you want to create Amazon links to.", 'amazon-associates-link-builder' ),
            "tracking_id_settings_info_message" => esc_html__( "For each marketplace you can add multiple tracking ids, separated by commas. The first tracking id will be considered as default tracking id for that marketplace.", 'amazon-associates-link-builder' )
        );
    }

    /**
     * Enqueue CSS classes
     *
     * @since 1.4.12
     *
     */
    public function aalb_credentials_enqueue_style() {
        wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'aalb_credentials_css', AALB_CREDENTIALS_CSS, array(), Plugin_Constants::PLUGIN_CURRENT_VERSION );
    }

    /**
     * Enqueue JS files
     *
     * @since 1.4.12
     *
     */
    public function aalb_credentials_enqueue_script() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'handlebars_js', Library_Endpoints::HANDLEBARS_JS );
        wp_enqueue_script( 'aalb_credentials_js', AALB_CREDENTIALS_JS, array( 'jquery', 'thickbox', 'handlebars_js' ), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        wp_localize_script( 'aalb_credentials_js', 'aalb_cred_data', $this->credentials_data() );
        wp_localize_script( 'aalb_credentials_js', 'aalb_cred_strings', $this->credentials_strings() );
    }

    /**
     * Returns list of marketplaces
     *
     *
     * @since 1.4.12
     * @return array marketplaces list
     */
    private function get_marketplace_list() {
        $config_loader = new Config_Loader();
        $aalb_marketplace_names = $config_loader->fetch_marketplaces();

        return json_encode( array_values( $aalb_marketplace_names ) );
    }

    /**
     * Prints admin error notices specific to geolite db on settings page
     *
     * @since 1.5.0
     */
    public function handle_error_notices() {
        try {
            if ( $this->is_more_than_one_marketplaces_configured() ) {
                $maxmind_db_manager = new Maxmind_Db_Manager( get_option( Db_Constants::CUSTOM_UPLOAD_PATH ), new Curl_Request(), new File_System_Helper() );
                $error_msg = $maxmind_db_manager->get_error_message();
                if ( ! empty( $error_msg ) ) {
                    aalb_error_notice( $error_msg );
                }
            }
        } catch ( \Exception $e ) {
            error_log( "Aalb_credentials_Helper::handle_error_notices:Unknown error:" . $e->getMessage() );
        }
    }

    /**
     * Checks if more than one marketplaces have been configured in settings
     *
     * @since 1.5.0
     *
     * @return bool True if more than one marketplaces configured in settings
     */
    private function is_more_than_one_marketplaces_configured() {
        return count( json_decode( get_option( Db_Constants::STORE_IDS ), true ) ) > 1;
    }
}

?>