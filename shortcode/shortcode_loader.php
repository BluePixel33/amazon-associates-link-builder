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

use AmazonAssociatesLinkBuilder\shortcode\Shortcode_Manager;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;

/**
 *
 * Registers the shortcode with the wordpress for rendering the product information.
 * Makes only a single instance of Aalb_Shortcode for rendering all the shortcodes.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/shortcode
 */
class Shortcode_Loader {
    public $shortcode_manager = null;

    /**
     * Register shortcode with Wordpress
     *
     * @since 1.0.0
     */
    public function add_shortcode() {
        add_shortcode( Plugin_Constants::SHORTCODE_AMAZON_LINK, array( $this, 'amazon_link_shortcode_callback' ) );
        add_shortcode( Plugin_Constants::SHORTCODE_AMAZON_TEXT, array( $this, 'amazon_textlink_shortcode_callback' ) );
    }

    /**
     * Callback function for rendering amazon_link shortcode
     *
     *
     * @since 1.0.0
     *
     * @param array $atts Shortcode attributes and values.
     *
     * @return HTML HTML for displaying the templates.
     */
    public function amazon_link_shortcode_callback( $atts ) {
        return $this->get_shortcode_manager_instance()->render( $atts );
    }

    /**
     * Callback function for rendering amazon_textlink shortcode
     *
     *
     * @since 1.4
     *
     * @param array $atts Shortcode attributes and values.
     *
     * @return HTML HTML for displaying the templates.
     */
    public function amazon_textlink_shortcode_callback( $atts ) {
        return $this->get_shortcode_manager_instance()->render( $atts );
    }

    /**
     * Create only a single instance of the Aalb Shortcode manager.
     * No need to create an instance for rendering each shortcode.
     *
     * @since 1.5.0
     * @return Shortcode_Manager The instance of Aalb_Shortcode.
     */
    public function get_shortcode_manager_instance() {
        if ( is_null( $this->shortcode_manager ) ) {
            $this->shortcode_manager = new Shortcode_Manager();
        }

        return $this->shortcode_manager;
    }
}

?>
