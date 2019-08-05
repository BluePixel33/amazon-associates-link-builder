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

use AmazonAssociatesLinkBuilder\includes\Remote_Loader;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;

/**
 * Fired while making a GET request.
 *
 * Generic class that can be used by any class to get the data from the cache.
 * If the data is not available in the cache, a remote GET request is made.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cache
 */
class Marketplace_Config_Cache_Loader {

    private $loader;
    private $helper;

    public function __construct( Remote_Loader $loader, Plugin_Helper $plugin_helper ) {
        $this->loader = $loader;
        $this->helper = $plugin_helper;
    }

    /**
     * If the information is in the cache, then retrieve the information from the cache.
     * Else get the information by making a GET request.
     *
     * @since 1.8.0
     *
     * @param string $key       Unique identification of the information.
     * @param string $url       URL for making a request.
     * @param string $link_code Link Code to be entered in URLS for attribution purposes.
     *
     * @return string GET Response.
     */
    public function get( $key, $url ) {
        $info = $this->lookup( $key );

        return $info !== false ? $info : $this->create_cache_entry( $key, $url );
    }

    /**
     * Lookup in the cache for a particular key.
     * If the key exists in the cache, the data is return.
     * Else false is returned.
     *
     * @since 1.8.0
     *
     * @param string $key Unique identification of the information.
     *
     * @return string  Data in the cache.
     */
    protected function lookup( $key ) {
        return get_transient( $key );
    }

    /**
     * Load the information with a GET request and save it in the cache. Return the loaded information.
     *
     * @since 1.8.0
     *
     * @param string $key       Unique identification of the information.
     * @param string $url       URL for making a request.
     * @param string $link_code Link Code to be entered in URLS for attribution purposes.
     *
     * @return string  GET Response.
     */
    protected function create_cache_entry( $key, $url ) {
        $info = $this->loader->load( $url );
        set_transient( $key, $info, AALB_CACHE_FOR_MARKETPLACE_CONFIG_TTL );

        return $info;
    }
}

?>
