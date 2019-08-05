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
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;

/**
 * Hepler class for validations used in the plugin.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/lib/php
 */
class Validation_Helper {

    protected $config_loader;
    protected $helper;

    public function __construct() {
        $this->config_loader = new Config_Loader();
        $this->helper = new Plugin_Helper();
    }

    /**
     * Validate ASIN
     *
     * @since 1.0.0
     *
     * @param string $asins ASIN
     *
     * @return bool  TRUE if the asin is valid, FALSE otherwise
     */
    public function validate_asin( $asin ) {
        return preg_match( '/^([0-9][0-9]{8}[0-9X]|[A-Z][A-Z0-9]{9})$/', trim( $asin ) );
    }

    /**
     * Validate Template Name
     *
     * @since 1.0.0
     *
     * @param string $template template name
     *
     * @return bool  TRUE if the template name is valid, FALSE otherwise
     */
    public function validate_template_name( $template ) {
        $aalb_template_names = get_option( Db_Constants::TEMPLATE_NAMES );

        return in_array( $template, $aalb_template_names );
    }

    /**
     * Validate Link ID
     * The link id should be alphanumeric inlcude hyphens (-) to be valid
     *
     * @since 1.0.0
     *
     * @param string $link_id Link ID from shortcode
     *
     * @return bool TRUE if the link id is valid, FALSE otherwise
     */
    public function validate_link_id( $link_id ) {
        return ctype_alnum( str_replace( array( '-' ), '', $link_id ) );
    }

    /**
     * Validate Marketplace
     *
     * @since 1.0.0
     *
     * @param string $marketplace Marketplace Abbreviation from shortcode
     *
     * @return bool TRUE if the marketplace is valid, FALSE otherwise
     */
    public function validate_marketplace( $marketplace ) {
        $aalb_marketplace_names = $this->config_loader->fetch_marketplaces();

        return in_array( $marketplace, $aalb_marketplace_names );
    }

    /**
     * Validate Store ID
     *
     * @since 1.0.0
     *
     * @param string $store_id Associate Tag from Shortcode
     *
     * @return bool TRUE if the Associate Tag is valid, FALSE otherwise
     */
    public function validate_store_id( $store_id ) {
        $aalb_store_id_names = explode( "\r\n", get_option( Db_Constants::STORE_ID_NAMES ) );
        //If the store id used is "not-specified".
        if ( $store_id === Db_Constants::DEFAULT_STORE_ID_NAME ) {
            $this->helper->show_error_in_preview( esc_html__( "Associate Tag was not found. The sales will not be attributed to any store and you will not earn the associate fees for it. Please provide a valid Associate Tag if you wish to earn the referral fees. Assocaite Tags can be configured from the 'Settings' tab in the WordPress administration panel", 'amazon-associates-link-builder' ) );

            return true;
        }

        return in_array( $store_id, $aalb_store_id_names );
    }

}

?>
