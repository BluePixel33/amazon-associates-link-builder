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

namespace AmazonAssociatesLinkBuilder\includes;

use AmazonAssociatesLinkBuilder\cache\Item_Lookup_Response_Cache;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;

/**
 * Fired during the plugin activation
 *
 * Gets the template names from the template directory and loads it into the database.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Activator {
    private $item_lookup_response_cache;

    public function __construct() {
        $this->item_lookup_response_cache = new Item_Lookup_Response_Cache( new Sql_Helper( DB_NAME, Db_Constants::ITEM_LOOKUP_RESPONSE_TABLE_NAME  ) );
    }
    /**
     * Add the template names to the database from the filesystem.
     *
     * @since 1.0.0
     */
    private function load_templates() {
        $plugin_helper = new Plugin_Helper();
        $plugin_helper->refresh_template_list();
    }

    /**
     * The code to run on activation
     *
     * @since 1.4.3
     */
    function activate() {
        $this->load_templates();
        $this->item_lookup_response_cache->init();
    }
}

?>
