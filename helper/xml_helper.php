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
            XML_Constants::TITLE           => ! empty( $item->ItemInfo->Title->DisplayValue ) ? $item->ItemInfo->Title->DisplayValue : null,
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
            XML_Constants::LARGE_IMAGE_URL  => ! empty( $item->Images->Primary->Large ) ? $item->Images->Primary->Large->URL : null,
            XML_Constants::MEDIUM_IMAGE_URL => ! empty( $item->Images->Primary->Medium ) ? $item->Images->Primary->Medium->URL : null,
            XML_Constants::SMALL_IMAGE_URL  => ! empty( $item->Images->Primary->Small ) ? $item->Images->Primary->Small->URL : null,
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
        if ( ! empty( $item->ItemInfo->ByLineInfo->Contributors ) ) {
            foreach ( $item->ItemInfo->ByLineInfo->Contributors as $author ) {
                array_push( $author_array, $author->Name );
            }
        }
        if ( ! empty( $item->ItemInfo->ByLineInfo->Brand ) ) {
            array_push( $brand_array, $item->ItemInfo->ByLineInfo->Brand->DisplayValue );
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
        $listing = null;
        if ( ! empty( $item->Offers->Listings ) ) {
            $listing = $item->Offers->Listings->Item[0];
        }
        return array(
            XML_Constants::SAVING         => ! empty( $listing ) ? $listing->Price->Savings->DisplayAmount : null,
            XML_Constants::SAVING_VALUE   => ! empty( $listing ) ? intval( $listing->Price->Savings->Amount*100 ) : null,
            XML_Constants::SAVING_PERCENT => ! empty( $listing ) ? $listing->Price->Savings->Percentage : null
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
        $new = null;
        if ( ! empty( $item->Offers->Summaries ) ) {
            foreach ( $item->Offers->Summaries->Item as $summary ) {
                if ( $summary->Condition->Value == "New" )
                    $new = $summary;
            }
        }
        return array(
            XML_Constants::MINIMUM_PRICE       => ! empty( $new ) ? $new->LowestPrice->DisplayAmount : null,
            XML_Constants::MINIMUM_PRICE_VALUE => ! empty( $new ) ? intval( $new->LowestPrice->Amount*100 ) : null
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
        $listing = null;
        if ( ! empty( $item->Offers->Listings ) ) {
            $listing = $item->Offers->Listings->Item[0];
        }
        return ! empty( $listing ) ? $listing->DeliveryInfo->IsPrimeEligible : null;
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
        $listing = null;
        if ( ! empty( $item->Offers->Listings ) ) {
            $listing = $item->Offers->Listings->Item[0];
        }
        return ! empty( $listing ) ? $listing->MerchantInfo->Name : null;
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
        $listing = null;
        if ( ! empty( $item->Offers->Listings ) ) {
            $listing = $item->Offers->Listings->Item[0];
        }
        $list_price = ! empty( $listing ) ? $listing->SavingBasis->DisplayAmount : null;
        $price = ! empty( $listing ) ? $listing->SavingBasis->DisplayAmount : null;
        $sale_price = ! empty( $listing ) ? $listing->Price->DisplayAmount : null;
        $list_price_amount = ! empty( $listing ) ? intval( $listing->SavingBasis->Amount*100 ) : null;
        $price_amount = ! empty( $listing ) ? intval( $listing->SavingBasis->Amount*100 ) : null;
        $sale_price_amount = ! empty( $listing ) ? intval( $listing->Price->Amount*100 ) : null;

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
        $listing = null;
        if ( ! empty( $item->Offers->Listings ) ) {
            $listing = $item->Offers->Listings->Item[0];
        }
        $new = null;
        if ( ! empty( $item->Offers->Summaries ) ) {
            foreach ( $item->Offers->Summaries->Item as $summary ) {
                if ( $summary->Condition->Value == "New" )
                    $new = $summary;
            }
        }
        $total_new = isset( $new ) ? $new->OfferCount : null;
        $availability = isset( $listing ) ? $listing->Availability->Type : null;

        return $total_new == '0' or $availability == XML_Constants::OUT_OF_STOCK_VALUE or empty( $total_new ) or empty( $availability );
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