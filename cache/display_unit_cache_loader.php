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

use AmazonAssociatesLinkBuilder\configuration\Config_Helper;
use AmazonAssociatesLinkBuilder\exceptions\Invalid_Marketplace_Exception;
use AmazonAssociatesLinkBuilder\rendering\Template_Engine;
use AmazonAssociatesLinkBuilder\rendering\Impression_Generator;

/**
 * Cache Loader for rendered templates.
 *
 * Loads ands saves the display unit in the cache.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cache
 */
class Display_Unit_Cache_Loader {

    private $item_lookup_response_cache_loader;
    private $template_engine;
    private $impression_generator;

    public function __construct( Template_Engine $template_engine, Item_Lookup_Response_Cache_Loader $item_lookup_response_cache_loader, Impression_Generator $impression_generator ) {
        $this->template_engine = $template_engine;
        $this->item_lookup_response_cache_loader = $item_lookup_response_cache_loader;
        $this->impression_generator = $impression_generator;
    }

    /**
     * Get the html of the display unit from the cache
     *
     * @since 1.8.0
     *
     * @param string $display_unit_cache_key Key to lookup in transients cache
     * @param string $template
     * @param string $marketplace Marketplace
     * @param string $link_code Link code
     * @param string $store_id Store_id
     * @param string $asin_group Group of asins separated by ","
     *
     * @return string  HTML of the display unit.
     */
    public function get( $display_unit_cache_key, $template, $marketplace, $link_code, $store_id, $asin_group ) {
        $display_unit = $this->lookup( $display_unit_cache_key );
        $asins_array = explode( ",", $asin_group );

        return $display_unit === false ? $this->create_cache_entry( $display_unit_cache_key, $template, $marketplace, $link_code, $store_id, $asins_array ) : $display_unit;
    }

    /**
     * Lookup in transient cache for the display unit
     *
     * @since 1.8.0
     *
     * @param string $display_unit_cache_key Key to search for in transients cache
     *
     * @return String | false Return response cached in transient
     */
    protected function lookup( $display_unit_cache_key ) {
        return get_transient( $display_unit_cache_key );
    }

    /**
     * Create the display unit from response returned by level 2 cache
     *
     * @since 1.8.0
     *
     * @param string $template
     *
     * @return String Response from level 2 cache
     */
    protected function create_cache_entry( $display_unit_cache_key, $template, $marketplace, $link_code, $store_id, $asins_array ) {
        $products = $this->item_lookup_response_cache_loader->get( $marketplace, $link_code, $store_id, $asins_array );

        $display_unit = $this->template_engine->render_xml( $products, $template );
        $display_unit = $this->add_html_for_impression_tracking( $display_unit, $marketplace, $link_code, $store_id, $asins_array );

        // Add entry in the cache only if products information of all requested asins is returned
        if ( sizeof( $products->Item ) === sizeof( $asins_array ) ) {
            set_transient( $display_unit_cache_key, $display_unit, AALB_CACHE_FOR_ASIN_ADUNIT_TTL );
        }

        return $display_unit;
    }

    /**
     * Adds pixel image HTML element to the display unit
     *
     * @since 1.6.0
     *
     * @param string $display_unit HTML of the display unit.
     * @param String $marketplace  marketplace
     * @param String $store_id     Store id of associate
     * @param String $link_code    Link code used for tracking
     * @param array $asins_array   Array of asins
     *
     * @return string $display_unit HTML of the display unit along with pixel image
     */
    public function add_html_for_impression_tracking( $display_unit, $marketplace, $link_code, $store_id, $asins_array ) {
        try {
            $impression = $this->impression_generator->get_impression( $marketplace, $link_code, $store_id, $asins_array );
            $display_unit = $impression . $display_unit;
        } catch ( Invalid_Marketplace_Exception $e ) {
            //Do Nothing as it is because of a new marketplace added and we are currently not racling impression for this new marketplace.
        } catch ( \InvalidArgumentException $e ) {
            error_log( "Aalb_Template_Engine::add_html_for_impression_tracking " . $e->getMessage() );
        } catch ( \Exception $e ) {
            error_log( "Aalb_Template_Engine::add_html_for_impression_tracking " . $e->getMessage() );
        }

        return $display_unit;
    }

}

?>
