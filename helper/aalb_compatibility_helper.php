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
 * The class responsible for handling all the functionalities related to
 * plugin compatibility with user environment
 *
 * @since      1.4.3
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 *
 * CAUTION: Any function present here should contain code that is compatible with at least PHP 5.3(even lower if possible) so
 * that anyone not meeting compatibility requirements for min php versions gets deactivated successfully.
 */
class Aalb_Compatibility_Helper {

    /**
     * Checks whether PHP version of user is compatible with plugin
     *
     * @since 1.4.3
     *
     * @return bool is_php_version_compatible
     */
    private function is_php_version_compatible() {
        return version_compare( phpversion(), AALB_PLUGIN_MINIMUM_SUPPORTED_PHP_VERSION, ">=" );
    }

    /**
     * Checks if the plugin is compatible with user environment or not
     * Add more compatibility checks whenever required
     *
     * @since 1.4.3
     *
     * @return bool is_plugin_compatible
     */
    public function is_plugin_compatible() {
        return $this->is_php_version_compatible();
    }

    /**
     * Prints the reason for User environment not being compatible as notice on admin page
     *
     * @since 1.4.3
     */
    public function incompatible_environment_message() {
        printf( "<div class=\"notice notice-error\">
               <h3>Amazon Associates Link Builder Plugin Not Activated!</h3>
               <p><span style=\"color:red;\">%s plugin requires PHP Version %s or higher. You’re still on %s.</span>
               </p></div>", AALB_PLUGIN_NAME, AALB_PLUGIN_MINIMUM_SUPPORTED_PHP_VERSION, phpversion() );
    }

    /**
     * Deactivates the plugin
     *
     * @since 1.4.3
     */
    public function deactivate() {
        //To remove the "Plugin Activated" Admin Notice
        unset( $_GET['activate'] );

        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $plugin = plugin_basename( AALB_PLUGIN_DIR . 'amazon-associates-link-builder.php' );
        deactivate_plugins( $plugin );
        //Remove action_links from admin page present below the plugin
        remove_filter( 'plugin_action_links_' . $plugin, 'add_action_links' );
        add_action( 'all_admin_notices', array( $this, 'incompatible_environment_message' ) );
    }

}

?>