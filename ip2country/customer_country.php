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

namespace AmazonAssociatesLinkBuilder\ip2Country;

use AmazonAssociatesLinkBuilder\ip2Country\Customer_Ip_Address;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;
use GeoIp2\Database\Reader;
/**
 *
 * Gets the Country from which customer is coming using his IP Address
 *
 * @since      1.5.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/ip2country
 */

class Customer_Country {
    private $customer_ip_address;
    private $helper;

    public function __construct() {
        $this->customer_ip_address = new Customer_Ip_Address();
        $this->helper = new Plugin_Helper();
    }

    /**
     * Gets the country of the customer from ip Address
     *
     * @since 1.5.0
     *
     * @return string Country of the customer
     */
    public function get_country_iso_code() {
        $country_code = "";
        $reader = $this->get_reader();
        if ( $reader ) {
            try {
                $ip = $this->customer_ip_address->get();
                $record = $reader->country( $ip );
                $country_code = $record->country->isoCode;
                //In the ISO code list, "GB" is used to refer to "UK" but since in Amazon we call it UK, so override that
                $country_code = $country_code === "GB" ? "UK" : $country_code;
            } catch ( \Exception $e ) {
                error_log( "Customer_Country:get_country_iso_code failed." . $e->getMessage() );
            }
            $reader->close();
        }

        return $country_code;
    }

    /**
     * Gets the instance of reader class of maxmind with GeoLiteCountryDB
     *
     * @since 1.5.0
     *
     * @return Reader Instance of Reader class of Maxmind
     */
    private function get_reader() {
        $reader = null;
        try {
            $maxmind_db_file_path = $this->get_maxmind_db_file_path();
            if ( ! empty( $maxmind_db_file_path ) ) {
                $reader = new Reader( $maxmind_db_file_path );
            }
        } catch ( \Exception $e ) {
            error_log( "Customer_Country:get_reader failed." . $e->getMessage() );
        }

        return $reader;
    }

    /**
     * Gets the maxmind db file name with complete path
     *
     * @since 1.5.0
     *
     * @return String Maxmind db file name with complete path if file is readable else null
     */
    private function get_maxmind_db_file_path() {
        $maxmind_db_file_path = get_option( Db_Constants::CUSTOM_UPLOAD_PATH ) . Plugin_Constants::MAXMIND_DATA_FILENAME;

        return ( file_exists( $maxmind_db_file_path ) && is_readable( $maxmind_db_file_path ) ) ? $maxmind_db_file_path : null;
    }
}

?>