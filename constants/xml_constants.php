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

namespace AmazonAssociatesLinkBuilder\constants;

/**
 * Class for Holding XML Constants
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */
class XML_Constants {
    const ASIN = 'ASIN';
    const TITLE = 'Title';
    const DETAIL_PAGE_URL = 'DetailPageURL';
    const BY = 'By';
    const PRIME = 'Prime';
    const MERCHANT = 'Merchant';
    const LARGE_IMAGE_URL = 'LargeImageURL';
    const MEDIUM_IMAGE_URL = 'MediumImageURL';
    const SMALL_IMAGE_URL = 'SmallImageURL';

    //Price Realted Keys
    const SAVING = 'Saving';
    const SAVING_VALUE = 'SavingValue';
    const SAVING_PERCENT = 'SavingPercent';
    const MINIMUM_PRICE = 'MinimumPrice';
    const MINIMUM_PRICE_VALUE = 'MinimumPriceValue';
    const CURRENT_PRICE = 'CurrentPrice';
    const CURRENT_PRICE_VALUE = 'CurrentPriceValue';
    const STRIKE_PRICE = 'StrikePrice';
    const STRIKE_PRICE_VALUE = 'StrikePriceValue';
    const IN_STOCK = 'InStock';
    //Labels
    const PRICE_LABEL = 'PriceLabel';
    const STRIKE_PRICE_LABEL = 'StrikePriceLabel';
    const PRODUCTS_FROM_AMAZON_LABEL = 'ProductsFromAmazonLabel';
    //Translation keys
    const CHECK_ON_AMAZON = 'check_on_amazon';
    const OUT_OF_STOCK = 'out_of_stock';
    const PRICE = 'price';
    const STRIKE_PRICE_STRING = 'strike_price_string';
    const PRODUCTS_FROM_AMAZON = 'products_from_amazon';
    //Array Keys
    const SAVINGS_INFO = 'savings_info';
    const MIN_PRICE_INFO = 'min_price_info';
    const STRINGS = 'strings';

    const PA_API_LOW_PRICE = 'too low to display';
    const IN_STOCK_KEY_VALUE = 'Yes True';
    const OUT_OF_STOCK_VALUE = 'Out of Stock';
}

?>