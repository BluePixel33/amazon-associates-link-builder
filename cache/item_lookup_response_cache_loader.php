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

use AmazonAssociatesLinkBuilder\rendering\Xml_Manipulator;
use AmazonAssociatesLinkBuilder\helper\Paapi_Helper;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;
use AmazonAssociatesLinkBuilder\includes\Item_Lookup_Response_Manager;

/**
 * Load response from Asin_Response_Table if it exists and is valid and on cache miss fetch the same via making a call to ItemLookUpAPI.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cache
 */
class Item_Lookup_Response_Cache_Loader {

    private $sql_helper;
    private $xml_manipulator;
    private $paapi_helper;
    private $item_lookup_response_manager;
    private $item_lookup_response_cache;

    public function __construct( Xml_Manipulator $xml_manipulator, Paapi_Helper $paapi_helper, Item_Lookup_Response_Manager $item_lookup_response_manager, Sql_Helper $sql_helper, Item_Lookup_Response_Cache $item_lookup_response_cache ) {
        $this->sql_helper = $sql_helper;
        $this->xml_manipulator = $xml_manipulator;
        $this->paapi_helper = $paapi_helper;
        $this->item_lookup_response_manager = $item_lookup_response_manager;
        $this->item_lookup_response_cache = $item_lookup_response_cache;
    }

    /**
     * Load the response from Aalb_Asin_Response table and on cache miss fetch the same via making a call to ItemLookupAPI
     *
     * @since 1.8.0
     *
     * @param string $marketplace Marketplace
     * @param string $link_code Link code
     * @param string $store_id Store_id
     * @param array $asins_array Array of asins
     *
     * @return \SimpleXMLElement     Final updated response items
     */
    public function get( $marketplace, $link_code, $store_id, $asins_array ) {
        $items = $this->lookup( $marketplace, $asins_array );
        $items = $this->is_all_entries_present( $items, $asins_array ) ? $items : $this->merge_cached_and_non_cached_response_items( $items, $this->create_cache_entry( $items, $marketplace, $store_id, $asins_array ) );

        return $this->create_final_response( $items, $asins_array, $marketplace, $link_code, $store_id );
    }

    /**
     * Check if all the entries are present in response retrieved from cache
     *
     * @since 1.8.0
     *
     * @param array $items Asin => response map retrieved from cache
     * @param array $asins_array Array of asins
     *
     * @return bool
     */
    private function is_all_entries_present( $items, $asins_array ) {
        return sizeof( $items ) == sizeof( $asins_array );
    }

    /**
     * Merge the items of response found in cache and those not found in cache
     *
     * @since 1.8.0
     *
     * @param array $cached_response_items Array of items retrieved from cache
     * @param array $non_cached_response_items Array of items which were not in cache and so fetched by making a call to ItemLookUpAPI
     *
     * @return array Merged response of items found in cache and those not found in cache
     */
    private function merge_cached_and_non_cached_response_items( $cached_response_items, $non_cached_response_items ){
        return array_merge( $cached_response_items, $non_cached_response_items );
    }

    /**
     * Create final response from merged array of response of found asins and response loaded from PA-API of missing asins
     *
     * @since 1.8.0
     *
     * @param array $fetched_asins_responses Array of asin => response pairs
     * @param array $requested_asins         Array of asins (required to preserve order)
     *
     * @return \SimpleXMLElement Final response
     */
    private function create_final_response( $fetched_asins_responses, $requested_asins, $marketplace, $link_code, $store_id ) {
        $final_response = array();
        // Reorder the items array such that order of asins in shortcode is preserved
        foreach ( $requested_asins as $asin ){
            if ( array_key_exists( $asin, $fetched_asins_responses ) ) {
                $final_response[] = $fetched_asins_responses[$asin];
            }
        }

        $items_xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><root><Items>" . implode( "", $final_response ) . "</Items></root>";
        $modified_items_xml = $this->xml_manipulator->get_customized_response( $items_xml, $store_id, $link_code, $marketplace );

        return $modified_items_xml;
    }

    /**
     * Load the information with a GET request and save it in the cache. Return the loaded information.
     *
     * @since 1.8.0
     *
     * @param array $found_items Asin => response of found asins
     * @param string $marketplace Marketplace
     * @param string $store_id Store_id
     * @param array $asins_array Array of asins
     *
     * @return array Response broken into asin => response pairs.
     */
    protected function create_cache_entry( $found_items, $marketplace, $store_id, $asins_array ) {
        $found_asins_array = array_keys( $found_items );
        $missing_asins_array = $this->get_missing_asins( $asins_array, $found_asins_array );

        try {
            // Make a call to ItemLookUpAPI function
            $items_array = $this->item_lookup_response_manager->get_response( $marketplace, $missing_asins_array, $store_id );
            $this->item_lookup_response_cache->add( $items_array, $marketplace );
        } catch ( \Exception $e ) {
            $this->item_lookup_response_cache->add_empty_response( $missing_asins_array, $marketplace );
            error_log( 'Item_Lookup_Response_Cache_Loader :: create_cache_entry::' . $this->paapi_helper->get_error_message( $e->getMessage() ) );
            $items_array = array();
        }

        return $items_array;
    }

    /**
     * Lookup in the cache for given asins and marketplace.
     * If the key exists in the cache, the data is returned.
     * Else false is returned.
     *
     * @since 1.8.0
     *
     * @param string $marketplace Marketplace
     * @param array $asins_array Array of asins
     *
     * @return array    Data in the cache (array of array of asin and item_lookup_response).
     */
    protected function lookup( $marketplace, $asins_array ) {
        if ( sizeof( $asins_array ) === 0 ) {
            return array();
        }
        $lookup_result = $this->sql_helper->lookup_asin_response_in_table( $asins_array, $marketplace );

        $asin_response_map = array();
        foreach ( $lookup_result as $item ){
            if ( $this->is_valid_result( $item ) ){
                $asin_response_map[$item->asin] = $item->item_lookup_response;
            }
        }

        $this->sql_helper->update_last_access_time( $asins_array, $marketplace );

        return $asin_response_map;
    }

    /**
     * Check if the response is valid
     *
     * @since 1.8.0
     *
     * @param Object $response Object which represents an entry in cache
     *
     * @return bool
     */
    private function is_valid_result( $response ){
        return $response->is_valid === '1' && ! empty( $response->item_lookup_response );
    }

    /**
     * Get the asins not found in cache
     *
     * @since 1.8.0
     *
     * @param array $asins_array       Array of all asins
     * @param array $found_asins_array Array of asins found in cache
     *
     * @return array Array of missing asins
     */
    private function get_missing_asins( $asins_array, $found_asins_array ) {
        return array_diff( $asins_array, $found_asins_array );
    }
}

?>
