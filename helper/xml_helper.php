<?php

namespace AmazonAssociatesLinkBuilder\helper;

use AmazonAssociatesLinkBuilder\configuration\Config_Helper;
use AmazonAssociatesLinkBuilder\constants\XML_Constants;

/*
Copyright 2016-2018 Amazon.com, Inc. or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License as published by the Free Software Foundation,
Version 2.0 (the "License"). You may not use this file except in compliance with the License.
A copy of the License is located in the "license" file accompanying this file.

This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,
either express or implied. See the License for the specific language governing permissions
and limitations under the License.
*/

/**
 * Helper class for customizations to the xml response
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/lib/php
 */
class Xml_Helper {
    private $config_helper;

    public function __construct( Config_Helper $config_helper ) {
        $this->config_helper = $config_helper;
    }

    /**
     * Gets Basic Labels object object containing price label, strike-price label & products from amazon label
     *
     * @since 1.8.0
     *
     * @param String $marketplace Marketplace according to which labels will be localized
     *
     * @return array Basic Labels
     */
    public function get_basic_labels( $marketplace ) {
        return array(
            XML_Constants::PRICE_LABEL                => $this->config_helper->get_string( XML_Constants::PRICE, $marketplace ),
            XML_Constants::STRIKE_PRICE_LABEL         => $this->config_helper->get_string( XML_Constants::STRIKE_PRICE_STRING, $marketplace ),
            XML_Constants::PRODUCTS_FROM_AMAZON_LABEL => $this->config_helper->get_string( XML_Constants::PRODUCTS_FROM_AMAZON, $marketplace )
        );
    }

    /**
     * Gets Basic info object containing ASIN, Title, Detail Page URL
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return  array $basic_info
     */
    public function get_basic_info( $item ) {
        return array(
            XML_Constants::ASIN            => ! empty( $item->ASIN ) ? $item->ASIN : null,
            XML_Constants::TITLE           => ! empty( $item->ItemAttributes->Title ) ? $item->ItemAttributes->Title : null,
            XML_Constants::DETAIL_PAGE_URL => ! empty( $item->DetailPageURL ) ? $item->DetailPageURL : null
        );
    }


    /**
     * Gets Image URL object containing LargeImage, MediumImage & SmallImage URLs.
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $items Well formed XML String: The Parent item node
     *
     * @return  array $image_urls
     */
    public function get_image_urls( $item ) {
        return array(
            XML_Constants::LARGE_IMAGE_URL  => ! empty( $item->LargeImage->URL ) ? $item->LargeImage->URL : null,
            XML_Constants::MEDIUM_IMAGE_URL => ! empty( $item->MediumImage->URL ) ? $item->MediumImage->URL : null,
            XML_Constants::SMALL_IMAGE_URL  => ! empty( $item->SmallImage->URL ) ? $item->SmallImage->URL : null,
        );
    }

    /**
     * Gets By Information String containing ' and 'separated strings of all artists, brands and authors
     * Sample Request:
     *
     *     [Author] => Array
     *      (
     *           [0] => Author1
     *           [1] => Author2
     *      )
     *
     *     [Artist] => Array
     *     (
     *           [0] => Artist1
     *     )
     *
     *     [Brand] => Array
     *     (
     *           [0] => Brand1
     *     )
     *
     * Sample Response: "Author1, Author2 and Brand1 and Artist1"
     *
     * @since 1.0.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return String By Node Information
     */
    public function get_by_information( $item ) {
        $author_array = array();
        $brand_array = array();
        $artist_array = array();
        $by_information = array();
        foreach ( $item->ItemAttributes->Author as $author ) {
            array_push( $author_array, $author );
        }
        foreach ( $item->ItemAttributes->Brand as $brand ) {
            array_push( $brand_array, $brand );
        }
        foreach ( $item->ItemAttributes->Artist as $artist ) {
            array_push( $artist_array, $artist );
        }
        if ( ! empty( $author_array ) ) {
            array_push( $by_information, implode( ', ', $author_array ) );
        }
        if ( ! empty( $brand_array ) ) {
            array_push( $by_information, implode( ', ', $brand_array ) );
        }
        if ( ! empty( $artist_array ) ) {
            array_push( $by_information, implode( ', ', $artist_array ) );
        }

        return implode( ' and ', $by_information );
    }

    /**
     * Gets Price Information String containing SavingsInfo, CurrentPrice, Strike Price, InStock and Minimum Price.
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return array $price_related_info
     */
    public function get_price_related_information( $item, $marketplace ) {
        $savings_info = $this->get_savings_info( $item );
        $min_price_info = $this->get_min_price_info( $item );
        $strike_price_and_current_price_info = $this->get_current_and_strike_price( $item, $savings_info[XML_Constants::SAVING_PERCENT] );
        $current_price = $strike_price_and_current_price_info[XML_Constants::CURRENT_PRICE];
        $current_price_value = $strike_price_and_current_price_info[XML_Constants::CURRENT_PRICE_VALUE];
        $strike_price = $strike_price_and_current_price_info[XML_Constants::STRIKE_PRICE];
        $strike_price_value = $strike_price_and_current_price_info[XML_Constants::STRIKE_PRICE_VALUE];

        if ( $this->is_out_of_stock( $item ) ) {
            $current_price = $this->config_helper->get_string( XML_Constants::OUT_OF_STOCK, $marketplace );
            $in_stock = null;
        } else {
            $in_stock = XML_Constants::IN_STOCK_KEY_VALUE;
        }

        if ( $this->is_price_too_low_to_display( $current_price ) ) {
            $current_price = $this->config_helper->get_string( XML_Constants::CHECK_ON_AMAZON, $marketplace );
        }

        return array(
            XML_Constants::SAVINGS_INFO        => $savings_info,
            XML_Constants::MIN_PRICE_INFO      => $min_price_info,
            XML_Constants::CURRENT_PRICE       => $current_price,
            XML_Constants::CURRENT_PRICE_VALUE => $current_price_value,
            XML_Constants::STRIKE_PRICE        => $strike_price,
            XML_Constants::STRIKE_PRICE_VALUE  => $strike_price_value,
            XML_Constants::IN_STOCK            => $in_stock
        );
    }

    /**
     * Gets Savings related nodes
     * Adds Amount saved in both raw and formatted way and the percentage saved.
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return array Node to which values are added
     */
    private function get_savings_info( $item ) {
        return array(
            XML_Constants::SAVING         => ! empty( $item->Offers->Offer->OfferListing->AmountSaved->FormattedPrice ) ? $item->Offers->Offer->OfferListing->AmountSaved->FormattedPrice : null,
            XML_Constants::SAVING_VALUE   => ! empty( $item->Offers->Offer->OfferListing->AmountSaved->Amount ) ? $item->Offers->Offer->OfferListing->AmountSaved->Amount : null,
            XML_Constants::SAVING_PERCENT => ! empty( $item->Offers->Offer->OfferListing->PercentageSaved ) ? $item->Offers->Offer->OfferListing->PercentageSaved : null
        );
    }

    /**
     * Get Minimum Price related information
     * Adds raw and formatted values of minimum price
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return array $min_price_info
     */
    private function get_min_price_info( $item ) {
        return array(
            XML_Constants::MINIMUM_PRICE       => ! empty( $item->OfferSummary->LowestNewPrice->FormattedPrice ) ? $item->OfferSummary->LowestNewPrice->FormattedPrice : null,
            XML_Constants::MINIMUM_PRICE_VALUE => ! empty( $item->OfferSummary->LowestNewPrice->Amount ) ? $item->OfferSummary->LowestNewPrice->Amount : null
        );
    }

    /**
     * Tells if the item is eligible for Prime
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return boolean is_prime_eligible
     */
    public function get_prime_eligibility( $item ) {
        return ! empty( $item->Offers->Offer->OfferListing->IsEligibleForPrime ) ? $item->Offers->Offer->OfferListing->IsEligibleForPrime : null;
    }

    /**
     * Get Merchant Name
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return String Merchant name if exists else null.
     */
    public function get_merchant_name( $item ) {
        return ! empty ( $item->Offers->Offer->Merchant->Name ) ? $item->Offers->Offer->Merchant->Name : null;
    }

    /**
     * Gets Current Price and Strike Price Info after applying logic
     * Logic for Current Price and Strike Price
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     * @param String $saving_percent
     *
     * @return array $strike_price_and_current_price_info
     */
    private function get_current_and_strike_price( $item, $saving_percent ) {
        $list_price = ! empty( $item->ItemAttributes->ListPrice->FormattedPrice ) ? $item->ItemAttributes->ListPrice->FormattedPrice : null;
        $price = ! empty( $item->Offers->Offer->OfferListing->Price->FormattedPrice ) ? $item->Offers->Offer->OfferListing->Price->FormattedPrice : null;
        $sale_price = ! empty( $item->Offers->Offer->OfferListing->SalePrice->FormattedPrice ) ? $item->Offers->Offer->OfferListing->SalePrice->FormattedPrice : null;
        $list_price_amount = ! empty( $item->ItemAttributes->ListPrice->Amount ) ? $item->ItemAttributes->ListPrice->Amount : null;
        $price_amount = ! empty( $item->Offers->Offer->OfferListing->Price->Amount ) ? $item->Offers->Offer->OfferListing->Price->Amount : null;
        $sale_price_amount = ! empty( $item->Offers->Offer->OfferListing->SalePrice->Amount ) ? $item->Offers->Offer->OfferListing->SalePrice->Amount : null;

        //Null is set to Zero on Typecasting
        $saving_percent = ! empty( $saving_percent ) ? (int) $saving_percent : 0;
        $strike_price = null;
        $strike_price_value = null;

        if ( ! empty( $sale_price_amount ) ) {
            //If Sale Price is returned
            $current_price = $sale_price;
            $current_price_value = $sale_price_amount;
            if ( $saving_percent > 1 ) {
                $strike_price = $price;
                $strike_price_value = $price_amount;
            }
        } else {
            $current_price = $price;
            $current_price_value = $price_amount;
            if ( $saving_percent > 1 ) {
                $strike_price = $list_price;
                $strike_price_value = $list_price_amount;
            }
        }

        return array(
            XML_Constants::STRIKE_PRICE        => $strike_price,
            XML_Constants::STRIKE_PRICE_VALUE  => $strike_price_value,
            XML_Constants::CURRENT_PRICE       => $current_price,
            XML_Constants::CURRENT_PRICE_VALUE => $current_price_value
        );
    }

    /**
     * Checks if an item is out of stock
     *
     * @since 1.8.0
     *
     * @param \SimpleXMLElement $item Well formed XML String: The Parent item node
     *
     * @return boolean is_out_of_stock
     */
    private function is_out_of_stock( $item ) {
        $total_new = isset( $item->OfferSummary->TotalNew ) ? $item->OfferSummary->TotalNew : null;
        $availability = isset( $item->Offers->Offer->OfferListing->Availability ) ? $item->Offers->Offer->OfferListing->Availability : null;

        return $total_new == '0' or $availability == XML_Constants::OUT_OF_STOCK_VALUE;
    }

    /**
     * Checks if Price of item is not present or Too low to display
     *
     * @since 1.8.0
     *
     * @param String $current_price
     *
     * @return boolean is_price_too_low_to_display
     */
    private function is_price_too_low_to_display( $current_price ) {
        return empty( $current_price ) or strtolower( $current_price ) == XML_Constants::PA_API_LOW_PRICE;
    }
}

?>