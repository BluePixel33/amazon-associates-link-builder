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

use AmazonAssociatesLinkBuilder\constants\Paapi_Constants;
use AmazonAssociatesLinkBuilder\helper\Paapi_Helper;
use AmazonAssociatesLinkBuilder\rendering\Xml_Manipulator;

/**
 * Class to manage item lookup response
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 */
class Item_Lookup_Response_Manager {

    private $xml_manipulator;
    private $paapi_helper;
    private $remote_loader;

    public function __construct( Xml_Manipulator $xml_manipulator ) {
        $this->xml_manipulator = $xml_manipulator;
        $this->paapi_helper = new Paapi_Helper();
        $this->remote_loader = new Remote_Loader();
    }

    /**
     * Parses the item lookup response and checks if the SIMPLE XML element object is generated successfully and has no error code from PA-API
     *
     * @since 1.8.0
     *
     * @param string $response Well-formed XML string
     *
     * @throws \Exception if xml object has an error code from PA-API
     */
    public function validate( $xml_response ) {
        if ( ! $this->should_render_xml( $xml_response ) ) {
            throw new \Exception( $xml_response->Items->Request->Errors->Error->Code );
        }
    }

    /**
     * Whether to allow xml to be rendered
     *
     * @since 1.4.7
     *
     * @param \SimpleXMLElement $xml Well-formed XML string
     *
     * @return boolean should_render_xml
     */
    private function should_render_xml( $xml ) {
        return ! isset( $xml->Items->Request->Errors->Error ) || $this->is_error_acceptable( $xml );
    }

    /**
     * Whether the error is acceptable. For now, It is acceptable for Invalid Parameter value with at least one item set.
     * This handles the case when an expired ASIN is present in a list of products and thus unblocks the ad rendering.
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $xml Well-formed XML string
     *
     * @return boolean is_error_acceptable
     */
    private function is_error_acceptable( $xml ) {
        return $xml->Items->Request->Errors->Error->Code == Paapi_Constants::INVALID_PARAMETER_VALUE_ERROR && isset( $xml->Items->Item );
    }

    /**
     * Get the item lookup response by creating required parameters and then making a GET request.
     *
     * @param String $marketplace marketplace
     * @param array $asins_array  array of asins
     * @param String $store_id    store id of associate
     *
     * @return array array of asin => response items
     */
    public function get_response( $marketplace, $asins_array, $store_id ) {
        $url = $this->paapi_helper->get_item_lookup_url( $asins_array, $marketplace, $store_id );
        $response = $this->remote_loader->load( $url );

        $xml_response = $this->xml_manipulator->parse( $response );
        $this->validate( $xml_response );

        $customized_response = $this->xml_manipulator->get_customized_items_object( $this->xml_manipulator->unescape_numeric_character_references( $response ), $marketplace );
        $items_array = $this->break_response_into_asin_response_map( $customized_response );

        return $items_array;
    }

    /**
     * Break the response into asin response map
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $response Xml response
     *
     * @return array Asin => response map
     */
    private function break_response_into_asin_response_map( $response ){
        $items_array = array();
        foreach ( $response->Item as $item ){
            $items_array[$item->ASIN->__toString()] = $item->asXML();
        }

        return $items_array;
    }
}
