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

namespace AmazonAssociatesLinkBuilder\rendering;

use AmazonAssociatesLinkBuilder\configuration\Config_Helper;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;

/**
 * Class to generate HTML element for impressions for impression tracking
 *
 * @since      1.6.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/lib/php
 */
class Impression_Generator {
    private $configuration_helper;

    public function __construct( Config_Helper $configuration_helper ) {
        $this->configuration_helper = $configuration_helper;
    }

    /*
     * @since 1.6.0
     *
     * @param String $marketplace_abbreviation Abbreviation of marketplace
     * @param String $store_id Store id of associate
     * @param String $link_code Link code used for tracking
     * @param String $asin ASIN of the Product
     *
     * @return String pixel_image
     *
     * @throws InvalidArgumentException
     *
     */
    public function get_impression( $marketplace_abbreviation, $link_code = Plugin_Constants::DEFAULT_LINK_CODE, $store_id, $asins ) {
        if ( empty( $store_id ) ) {
            throw new \InvalidArgumentException( "Empty Store Id passed" );
        }
        $impression_recorder_service_config = $this->configuration_helper->get( $marketplace_abbreviation, Plugin_Constants::IMPRESSION_RECORDER_SERVICE_KEY );
        $impression_recorder_service_endpoint = $impression_recorder_service_config[Plugin_Constants::ENDPOINT_KEY];
        $org_unit_id = $impression_recorder_service_config[Plugin_Constants::ORG_UNIT_ID_KEY];

        return $this->get_html_for_pixel_image_of_all_asins( $impression_recorder_service_endpoint, $store_id, $link_code, $org_unit_id, $asins );
    }

    /*
     * @since 1.6.0
     *
     * @param String $impression_recorder_service_endpoint
     * @param String $store_id
     * @param String $link_code
     * @param Integer $org_unit_id
     * @param Array $asins_array Array of asins
     *
     * @return String HTML for the pixel image of all asins in $asin_group
     *
     * @throws \InvalidArgumentException
     *
     */
    private function get_html_for_pixel_image_of_all_asins( $impression_recorder_service_endpoint, $store_id, $link_code, $org_unit_id, $asins_array ) {
        $pixel_image_for_all_asins = "";
        foreach ( $asins_array as $asin ) {
            $pixel_image_url = $this->get_url_with_query_params( $impression_recorder_service_endpoint, $store_id, $link_code, $org_unit_id, $asin );
            $pixel_image_for_all_asins .= $this->get_html_element_for_pixel_image( $pixel_image_url );
        }

        return $pixel_image_for_all_asins;
    }

    /*
     * @since 1.6.0
     *
     * @param String $impression_recorder_service_endpoint
     * @param String $store_id
     * @param String $link_code
     * @param Integer $org_unit_id
     * @param String $asin ASIN of the Product
     *
     * @return String URL with query params set for impression recording
     *
     */
    private function get_url_with_query_params( $impression_recorder_service_endpoint, $store_id, $link_code, $org_unit_id, $asin ) {
        return "https://" . $impression_recorder_service_endpoint . '/e/ir?' . 't=' . $store_id . '&l=' . $link_code . '&o=' . $org_unit_id . '&a=' . $asin;
    }

    /*
     * Get HTML element for pixel image. The wifth and height ares et to 1px each so that image is not visible
     *
     * @since 1.6.0
     *
     * @param  String $image_url URL of the image
     *
     * @return String HTML for the pixel image
     *
     */
    private function get_html_element_for_pixel_image( $image_url ) {
        /*
         *
         * Below are inline and marked as important to avoid any chance of stylesheet not loading or
         * property override on client side.The reason being if some other plugin overrides the
         * property, this pixel will disrupt page spacing a little bit.
         *
         */
        return '<img src="' . $image_url . '" width="1px" height="1px" alt="" style="position: fixed !important; bottom: -1px !important; right: -1px !important; border:none !important; margin:0px !important;" />';
    }
}

?>