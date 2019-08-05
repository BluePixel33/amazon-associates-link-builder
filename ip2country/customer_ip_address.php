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
/**
 *
 * Gets the IP Address of the customer
 *
 * @since      1.5.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/ip2country
 */
class Customer_Ip_Address {
    /**
     * Gets the IP Address of the customer
     *
     * @since 1.5.0
     *
     * @return string IP ADDRESS of the customer
     */
    public function get() {
        $ip_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
        $ip_list = explode( ',', $ip_address );
        $ip_list = array_map( array( $this, 'standardize_ip_address' ), $ip_list );

        if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $x_forwarded_for_ip_address = array_map( array( $this, 'standardize_ip_address' ), explode( ',', @$_SERVER["HTTP_X_FORWARDED_FOR"] ) );
            $ip_list = array_merge( $ip_list, $x_forwarded_for_ip_address );
            $trusted_proxies = array( '', '::1', '127.0.0.1' );
            $trusted_proxies = array_map( array( $this, 'standardize_ip_address' ), $trusted_proxies );

            $ip_list = array_diff( $ip_list, $trusted_proxies );
        }

        array_unshift( $ip_list, '::1' );
        $ip_address = end( $ip_list );

        if ( ! $ip_address ) {
            $ip_address = '::1';
        }

        return $ip_address;
    }

    /**
     * Standardize the IPV6 Address & Trim IPV4 Address
     *
     * @since 1.5.0
     *
     * @param string $ip_address Raw IP Address
     *
     * @return string Normalized IP Address
     */
    private function standardize_ip_address( $ip_address ) {
        $ip_address = trim( $ip_address );
        $binary_representation = @inet_pton( $ip_address );

        return empty( $binary_representation ) ? $ip_address : inet_ntop( $binary_representation );
    }

}

?>