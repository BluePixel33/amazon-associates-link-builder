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

namespace AmazonAssociatesLinkBuilder\sql;

use AmazonAssociatesLinkBuilder\constants\Cron_Constants;

/**
 * Sql helper class to construct various sql queries, query the database and return the results.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/sql
 */
class Sql_Helper {

    private $wpdb;
    private $table_name;
    private $database_name;

    public function __construct( $database_name, $table_name ) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
        $this->database_name = $database_name;
    }

    /**
     * Queries the database to add a table if it doesn't exist.
     *
     * @since 1.8.0
     *
     * @param array $columns      Column names, data_types and attributes
     * @param array $index        Index required in table
     * @param string $primary_key Primary key of the table
     */
    public function create_table( $columns, $index = array(), $primary_key = "" ) {
        if ( $this->table_not_exists( $this->database_name, $this->table_name ) ) {
            $columns_param = implode( ", ", $columns );
            $index_param = ! empty( $index ) ? ", KEY " . implode( ", KEY ", $index ) : "";
            $primary_key_param = ! empty( $primary_key ) ? ", PRIMARY KEY  $primary_key" : "";

            $parameters = $columns_param . $index_param . $primary_key_param;
            $create_table_query = "CREATE TABLE {$this->table_name} ( $parameters )";

            //To use dbDelta function
            require_once( ABSPATH . "/wp-admin/includes/upgrade.php" );
            dbDelta( $create_table_query );
        }
    }

    /**
     * Truncates the table.
     *
     * @since 1.8.0
     *
     * @return bool Indicating if the operation succeeded.
     */
    public function truncate_table() {
        return $this->wpdb->query( "TRUNCATE TABLE {$this->table_name}" );
    }

    /**
     * Drop the table.
     *
     * @since 1.8.0
     *
     * @return bool Indicating if the operation succeeded.
     */
    public function drop_table() {
        return $this->wpdb->query( "DROP TABLE {$this->table_name}" );
    }

    /**
     * Queries the database for given asins and their marketplace.
     *
     * @since 1.8.0
     *
     * @param array $asins_array  Array of asins
     * @param string $marketplace Marketplace
     *
     * @return mixed Returns the array of rows containing fields: item lookup response, asin and boolean indicating if the entry is valid (was updated in the last 30 min) else returns an empty array (if no rows were found or a database error occurred).
     */
    public function lookup_asin_response_in_table( $asins_array, $marketplace ) {
        $asins_param = implode( ",", array_map( function ( $asin ) {
            return "'{$asin}'";
        }, $asins_array ) );

        $lookup_in_table_query = "SELECT `item_lookup_response`, `asin`, `last_updated_time` > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 30 MINUTE ) AS `is_valid`
                                         FROM {$this->table_name}
                                         WHERE `asin` IN ( {$asins_param} ) AND `marketplace`='{$marketplace}'";

        return $this->wpdb->get_results( $lookup_in_table_query );
    }

    /**
     * Updates the last access time of entries of asins and their marketplace which were requested and were present in the table. (They may or may not have been valid (updated in the last 30 min) ).
     *
     * @since 1.8.0
     *
     * @param array $asins_array  Array of asins
     * @param string $marketplace Marketplace
     */
    public function update_last_access_time( $asins_array, $marketplace ) {
        $asins_param = implode( ",", array_map( function ( $asin ) {
            return "'{$asin}'";
        }, $asins_array ) );

        $update_last_access_query = "UPDATE {$this->table_name}
                                         SET `last_access_time`=CURRENT_TIMESTAMP()
                                         WHERE asin IN ({$asins_param}) AND marketplace='{$marketplace}'";

        $update_last_access_result = $this->wpdb->query( $update_last_access_query );
        if ( $update_last_access_result === false ) {
            error_log( "There was a problem while updating the table" );
        }
    }

    /**
     * Add rows in the table for given columns and values.
     *
     * @since 1.8.0
     *
     * @param array $columns                         Array of strings of column names
     * @param array $values                          Array of rows to be added in the table
     * @param array $on_duplicate_key_update_columns Array of columns to be updated when an entry with duplicate key exists
     *
     * @return bool Indicating if the operation succeeded.
     */
    public function add_rows_in_table( $columns, $values, $on_duplicate_key_update_columns = array() ) {
        $on_duplicate_key_update_columns_clause = ! empty( $on_duplicate_key_update_columns ) ? $this->get_on_duplicate_key_update_columns_clause( $on_duplicate_key_update_columns ) : "";
        $columns_param = implode( ", ", $columns );

        $values_param = implode( ",", array_map( function ( $value ) {
            return "(" . implode( ",", $value ) . ")";
        }, $values ) );

        $add_rows_query = "INSERT INTO {$this->table_name} (
                                         {$columns_param})
                                         VALUES {$values_param} {$on_duplicate_key_update_columns_clause}";

        return $this->wpdb->query( $add_rows_query );
    }

    /**
     * Get the clause in query for updating columns whenever an entry with duplicate key exists in the table.
     *
     * @since 1.8.0
     *
     * @param $on_duplicate_key_update_columns
     *
     * @return string
     */
    private function get_on_duplicate_key_update_columns_clause( $on_duplicate_key_update_columns ) {
        $on_duplicate_key_update_columns_clause = "ON DUPLICATE KEY UPDATE " . implode( ", ", array_map( function ( $column ) {
                return "{$column}=VALUES({$column})";
            }, $on_duplicate_key_update_columns ) );

        return $on_duplicate_key_update_columns_clause;
    }

    /* Delete entries from table which haven't been accessed in the last 24 hours
     *
     * @return bool Indicating if the operation succeeded.
     */
    public function delete_old_asins(){
        $delete_asins_query = "DELETE FROM {$this->table_name}
                 WHERE `last_access_time` < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . Cron_Constants::CACHE_EVICT_AGE . ")";

        return $this->wpdb->query($delete_asins_query);
    }

    /**
     * Get asins which are about to expire.
     *
     * @return array Array of rows containing fields: asin and marketplace.
     */
    public function get_asins_to_update() {
        $get_asins_to_update_query = "SELECT `asin`, `marketplace` 
                 FROM {$this->table_name}
                 WHERE `last_updated_time` < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL " . Cron_Constants::CACHE_REFRESH_AGE . ")
                 ORDER BY `last_access_time` DESC";

        return $this->wpdb->get_results( $get_asins_to_update_query );
    }

    /**
     * Checks if the table exists.
     *
     * @since 1.8.0
     *
     * @param string $database_name Name of the database
     * @param string $table_name    Name of the table
     *
     * @return bool Indicating if table exists.
     */
    private function table_not_exists( $database_name, $table_name ) {
        $is_table_exists_query = "SELECT COUNT(*) AS `number_of_tables`
                FROM information_schema.TABLES
                WHERE `table_schema` = '{$database_name}'
                AND `table_name` = '{$table_name}'";

        //get_results returns an array of objects and here, the first element of the array contains property number_of_tables
        return ( $this->wpdb->get_results( $is_table_exists_query )[0]->number_of_tables ) == 0;
    }

}