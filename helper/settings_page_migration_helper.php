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

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;

/**
 * Fired during the plugin activation
 *
 * Runs the migration logic to populate store-ids in default locale and also to handle backward compatibility
 * after release of new Settings page version(1.4.12)
 *
 * Temporary class. Needs to be removed after few versions once wide variety of audience
 * is on new Settings page version
 *
 * @since      1.4.12
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Settings_Page_Migration_Helper {
    /**
     * Runs the migrations logic to new settings page
     *
     * @since 1.4.12
     */
    public function run_migration_logic() {
        if ( version_compare( Plugin_Constants::MULTI_LOCALE_SETTINGS_PLUGIN_VERSION, get_option( Db_Constants::PLUGIN_VERSION ), ">" ) ) {
            delete_option( Db_Constants::STORE_IDS );
            $this->migrate_storeids();
        }
    }

    /**
     * Populates store ids in default locale
     *
     * @since 1.4.12
     */
    private function migrate_storeids() {
        $default_marketplace_name = get_option( Db_Constants::DEFAULT_MARKETPLACE, Db_Constants::DEFAULT_MARKETPLACE_NAME );
        $store_id_list = get_option( Db_Constants::STORE_ID_NAMES );
        if ( $store_id_list ) {
            $marketplace_store_id_mapping = array( $default_marketplace_name => explode( Plugin_Constants::NEWLINE_SEPARATOR, $store_id_list ) );
            update_option( Db_Constants::STORE_IDS, json_encode( $marketplace_store_id_mapping ) );
        }
    }
}

?>