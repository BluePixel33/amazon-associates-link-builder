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

namespace AmazonAssociatesLinkBuilder\cache;

use AmazonAssociatesLinkBuilder\sql\Sql_Helper;

/**
 * Load response from Asin_Response_Table if it exists and is valid and on cache miss fetch the same via making a call to ItemLookUpAPI.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cache
 */
class Item_Lookup_Response_Cache {

    private $sql_helper;

    public function __construct( Sql_Helper $sql_helper ) {
        $this->sql_helper = $sql_helper;
    }

    /**
     * Init cache by creating the table
     *
     * @since 1.8.0
     */
    public function init() {
        $columns = array(
            "`asin` VARCHAR(10) NOT NULL",
            "`marketplace` VARCHAR(5) NOT NULL",
            "`item_lookup_response` TEXT",
            "`last_updated_time` TIMESTAMP DEFAULT 0",
            "`last_access_time` TIMESTAMP DEFAULT 0"
        );

        $indices = array( "`index_last_updated_time` (`last_updated_time`)" );
        $primary_key = "(`asin`, `marketplace`)";

        $this->sql_helper->create_table( $columns, $indices, $primary_key );
    }

    /**
     * Clear the cache by truncating the table
     *
     * @since 1.8.0
     */
    public function clear() {
        $result = $this->sql_helper->truncate_table();
        if ( $result === false ) {
            error_log( 'There was a problem while truncating the table' );
        }
    }

    /**
     * Remove the cache by dropping table
     *
     * @since 1.8.0
     */
    public function delete() {
        $result = $this->sql_helper->drop_table();
        if ( $result === false ) {
            error_log( 'There was a problem while deleting the table' );
        }
    }

    /**
     * Adds entry in the cache for given asin, marketplace and response.
     *
     * @since 1.8.0
     *
     * @param array $items_array Array of asin => response pairs
     * @param string marketplace
     */
    public function add( $items_array, $marketplace, $update_last_access_time = true ) {
        $entries = array();

        foreach ( $items_array as $asin => $response ) {
            $escaped_response = esc_sql( $response );
            $entries[] = array( "'{$asin}'", "'{$marketplace}'", "'{$escaped_response}'", "CURRENT_TIMESTAMP()", "CURRENT_TIMESTAMP()" );
        }

        $columns = array( "`asin`", "`marketplace`", "`item_lookup_response`", "`last_updated_time`", "`last_access_time`" );
        $on_duplicate_key_update_columns = array( "`item_lookup_response`", "`last_updated_time`" );
        if( $update_last_access_time ){
            $on_duplicate_key_update_columns[] = "`last_access_time`";
        }

        $result = $this->sql_helper->add_rows_in_table( $columns, $entries, $on_duplicate_key_update_columns );
        if ( $result === false ) {
            error_log( "There was a problem while adding entry in the table" );
        }
    }

    /**
     * Add empty response entry for given asins and marketplace
     *
     * @since 1.8.0
     *
     * @param array $asins_array Array of asins
     * @param String $marketplace Marketplace
     */
    public function add_empty_response( $asins_array, $marketplace ){
        $entries = array();

        foreach ( $asins_array as $asin ) {
            $entries[] = array( "'{$asin}'", "'{$marketplace}'", "''", "0", "CURRENT_TIMESTAMP()" );
        }

        $columns = array( "`asin`", "`marketplace`", "`item_lookup_response`", "`last_updated_time`", "`last_access_time`" );
        $on_duplicate_key_update_columns = array( "`last_updated_time`", "`last_access_time`" );

        $result = $this->sql_helper->add_rows_in_table( $columns, $entries, $on_duplicate_key_update_columns );
        if ( $result === false ) {
            error_log( "There was a problem while adding entry in the table" );
        }
    }

    /**
     * Delete entries from table which haven't been accessed in the last 24 hours
     *
     * @since 1.8.0
     */
    public function delete_old_asins(){
        $result = $this->sql_helper->delete_old_asins();
        if( $result===False ){
            error_log("There was a problem while deleting asins from the table");
        }
    }

    /**
     * Get asins which are about to expire.
     *
     * @since 1.8.0
     */
    public function get_asins_to_update() {
        $result = $this->sql_helper->get_asins_to_update();
        if ($result===False){
            error_log("There was a problem while fetching asins to update from the table");
            $result = array();
        }
        return $result;
    }
}

?>