<?php

/**
 * @package AmazonAssociatesLinkBuilder
 *
 */

/*
Plugin Name: Amazon Associates Link Builder
Description: Amazon Associates Link Builder is the official free Amazon Associates Program plugin for WordPress. The plugin enables you to search for products in the Amazon catalog, access real-time price and availability information, and easily create links in your posts to products on Amazon.com. You will be able to generate text links, create custom ad units, or take advantage of out-of-the-box widgets that we’ve designed and included with the plugin.
Version: 1.9.3
Author: Amazon Associates Program
Author URI: https://affiliate-program.amazon.com/
License: GPLv2
Text Domain: amazon-associates-link-builder
Domain Path: /languages/
*/

/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}
/*
 * Minimum WP version supported in these activation, deactivation and updates check: 2.8.0
 * Minimum PHP version supported in these activation, deactivation and updates check: 5.2
 */
require_once( plugin_dir_path( __FILE__ ) . 'plugin_config.php' );
require_once( AALB_COMPATIBILITY_HELPER );
$aalb_compatibility_helper = new Aalb_Compatibility_Helper();
if ( ! $aalb_compatibility_helper->is_plugin_compatible() ) {
    $aalb_compatibility_helper->deactivate();
} else {
    require_once( AALB_INITIALIZER );
    $aalb_initializer = new Aalb_Initializer();
    $aalb_initializer->initialize( plugin_basename( __FILE__ ), __FILE__ );
}


//This will show Plugin in local language even if plugin is not activated or does not meet compatibilty requirements.
add_action( 'plugins_loaded', 'aalb_plugin_load_textdomain' );

/**
 * Adds a text-domain to facilitate translation feature
 * @since 1.4.8
 */
function aalb_plugin_load_textdomain() {
    load_plugin_textdomain( 'amazon-associates-link-builder', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

?>
