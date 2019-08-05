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

namespace AmazonAssociatesLinkBuilder\configuration;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\exceptions\Invalid_Marketplace_Exception;
use AmazonAssociatesLinkBuilder\constants\XML_Constants;

/**
 * Helper class for dealing with configuration
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/configuration
 */
class Config_Helper {
    private $configuration;

    public function __construct() {
        $this->configuration = $this->get_configuration();
    }

    /*
    * Parse the JSON from File and returns Marketplace Array from that.
    *
    * @return JSONObject
    *
    * @since 1.8.0
    *
    */
    private function get_configuration() {
        $body = json_decode( file_get_contents( AALB_MARKETPLACE_CONFIG_JSON ), true );
        if ( json_last_error() !== \JSON_ERROR_NONE ) {
            error_log( 'Invalid Json read from file' . json_last_error() );
            $body = null;
        } else {
            $body = $body['Local']['Marketplace'];
        }

        return $body;
    }

    /*
    * Return the configuration value for the key & marketplace
    *
    * @param String $marketplace
    * @param String $key
    *
    * @return Object Value for the key
    *
    * @throws \InvalidArgumentException for non-existent keys
    *
    * @since 1.8.0
    *
    */
    public function get( $marketplace, $key ) {
        if ( ! isset( $this->configuration ) ) {
            throw new \Exception( "Marketplace Configuration is not set" );
        } else if ( ! isset( $this->configuration[$marketplace] ) ) {
            throw new Invalid_Marketplace_Exception( "Invalid marketplace " . $marketplace . " passed" );
        } else if ( ! isset( $this->configuration[$marketplace][$key] ) ) {
            throw new \InvalidArgumentException( "Invalid key " . $key . " passed" );
        } else {
            return $this->configuration[$marketplace][$key];
        }
    }

    /**
     * Return strings for a marketplace
     *
     * @since 1.8.0
     *
     * @param string $key         Identifier of string to be translated
     * @param string $marketplace The target marketplace name
     *
     * @return string
     */
    public function get_string( $key, $marketplace ) {
        try {
            $string_array = $this->get( $marketplace, XML_Constants::STRINGS );
        } catch ( Invalid_Marketplace_Exception $e ) {
            //Sending EN_US strings. Case when a new marketplace is added
            $string_array = $this->get( Db_Constants::DEFAULT_MARKETPLACE_NAME, XML_Constants::STRINGS );
        } catch ( \Exception $e ) {
            error_log( "Aalb_Config_Helper::get_string::" . $e->getMessage() );
            $string_array = null;
        }

        return ( isset( $string_array ) && isset( $string_array[$key] ) ) ? $string_array[$key] : $key;
    }

}

?>