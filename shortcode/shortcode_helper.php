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
namespace AmazonAssociatesLinkBuilder\shortcode;

use AmazonAssociatesLinkBuilder\configuration\Config_Loader;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\helper\Validation_Helper;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;

/**
 * Helper class for AALB shortcodes.
 *
 * Contains helper functions used while rendering shortcodes
 *
 * @since      1.4
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/shortcode
 */
class Shortcode_Helper {

    protected $helper;
    protected $config_loader;
    protected $validation_helper;

    public function __construct() {
        $this->helper = new Plugin_Helper();
        $this->config_loader = new Config_Loader();
        $this->validation_helper = new Validation_Helper();
    }

    /**
     * Add CSS for the template
     *
     * @since 1.0.0
     */
    public function enqueue_template_styles( $template_name ) {
        $aalb_default_templates = explode( ",", Plugin_Constants::AMAZON_TEMPLATE_NAMES );
        if ( in_array( $template_name, $aalb_default_templates ) ) {
            wp_enqueue_style( 'aalb_template' . $template_name . '_css', AALB_TEMPLATE_URL . $template_name . '.css', array(), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        } else {
            $aalb_template_upload_url = $this->helper->get_template_upload_directory_url();
            wp_enqueue_style( 'aalb_template' . $template_name . '_css', $aalb_template_upload_url . $template_name . '.css', array(), Plugin_Constants::PLUGIN_CURRENT_VERSION );
        }
    }

    /**
     * Format comma separated asins into hypen separated asins for building key.
     * Checks for more spaces and trims it.
     *
     * @since 1.0.0
     *
     * @param string $asins Comma separated asins.
     *
     * @return string Hyphen separated asins.
     */
    public function format_asins( $asins ) {
        return preg_replace( '/[ ,]+/', '-', trim( $asins ) );
    }

    /**
     * Get validated link-id
     * Checks if the link id we got from the api is valid or not and returns
     * validated link-id. In case of invalid marketplace, it returns empty string.
     *
     * @since 1.0.0
     *
     * @param string $marketplace Marketplace from shortcode
     *
     * @return string  $validated_template Validated marketplace
     */
    public function get_validated_link_id( $link_id ) {
        $validated_link_id = $link_id;
        if ( ! $this->validation_helper->validate_link_id( $link_id ) ) {
            //If the link id is not valid, return empty string
            $validated_link_id = '';
        }

        return $validated_link_id;
    }

    /**
     * Get validated marketplace.
     * Checks if a marketplace abbreviation from shortcode is valid and returns
     * validated marketplace. In case of invalid marketplace, it returns default marketplace.
     *
     * @since 1.0.0
     *
     * @param string $marketplace Marketplace from shortcode
     *
     * @return string  $validated_template Validated marketplace
     */
    public function get_validated_marketplace( $marketplace ) {
        //Changing case of the marketplace to upper. Ensures case insensitivity
        $validated_marketplace = strtoupper( $marketplace );
        if ( ! $this->validation_helper->validate_marketplace( $marketplace ) ) {
            //If the marketplace is not valid, return default marketplace
            $validated_marketplace = get_option( Db_Constants::DEFAULT_MARKETPLACE );
        }

        return $validated_marketplace;
    }

    /**
     * Get validated asin list
     * Drops invalid asin from the list
     *
     * @since 1.0.0
     *
     * @param string $asins List of asins from shortcode
     *
     * @return string List of validated asins
     */
    public function get_validated_asins( $asins ) {
        //Creates array of asins in the shortcode
        $asins_array = explode( ',', $asins );
        foreach ( $asins_array as $asin ) {
            if ( ! $this->validation_helper->validate_asin( $asin ) ) {
                //Drop Invalid ASIN out of list of asins
                $asins_array = array_diff( $asins_array, array( $asin ) );
                //Show error message regarding incorrect asin in preview mode only
                /* translators: %s: Invalid ASIN name */
                $this->helper->show_error_in_preview( sprintf( esc_html__( "The ASIN: %s is invalid.", 'amazon-associates-link-builder' ), $asin ) );
            }
        }

        return implode( ',', $asins_array );
    }

    /**
     * Get validated template.
     * Checks if a template is valid, returns default template otherwise
     *
     * @since 1.0.0
     *
     * @param string $template Template name from shortcode
     *
     * @return string  $validated_template Validated template name
     */
    public function get_validated_template( $template ) {
        $validated_template = $template;
        if ( ! $this->validation_helper->validate_template_name( $template ) ) {
            //Return Default template in case of invalid template name
            $validated_template = get_option( Db_Constants::DEFAULT_TEMPLATE );
            //Show error message regarding incorrect asin in preview mode only
            /* translators: 1: Invalid template name 2: Valid template name */
            $this->helper->show_error_in_preview( sprintf( esc_html__( "The template: %1s is invalid. Using default template %2s.", 'amazon-associates-link-builder' ), $template, $validated_template ) );
        }

        return $validated_template;
    }

    /**
     * Get validated store id.
     * Checks if a store id is valid, returns default store id otherwise
     *
     * @since 1.0.0
     *
     * @param string $store_id Store ID from shortcode
     *
     * @return string  $validated_store_id Validated Store ID
     */
    public function get_validated_store_id( $store_id ) {
        $validated_store_id = $store_id;
        if ( ! $this->validation_helper->validate_store_id( $store_id ) ) {
            //Return Default store id in case of invalid store id
            $validated_store_id = get_option( Db_Constants::DEFAULT_STORE_ID, Db_Constants::DEFAULT_STORE_ID_NAME );
            //Show error message regarding incorrect asin in preview mode only
            /* translators: 1: Invalid associate id 2: Valid associate id */
            $this->helper->show_error_in_preview( sprintf( esc_html__( "The Associate tag %1s is not present in the list of valid tags. Associate tag has been updated to %2s. Please check your Associate tag selection or contact the administrator to add a new tag.", 'amazon-associates-link-builder' ), $store_id, $validated_store_id ) );
        }

        return $validated_store_id;
    }
}

?>
