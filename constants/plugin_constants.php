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
 * Class for Holding Plugin Constants
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class Plugin_Constants {
    const PLUGIN_CURRENT_VERSION = '1.9.3';
    //Version no. with multi locale settings page
    const MULTI_LOCALE_SETTINGS_PLUGIN_VERSION = '1.4.12';
    const WORDPRESS_REQUEST_TIMEOUT_IN_MS = 40000;

    //Support Email-Id
    const SUPPORT_EMAIL_ID = 'link-builder@amazon.com';

    const NEWLINE_SEPARATOR = "\r\n";
    const GEO_TARGETED_LINKS_DELIMITER = "|";

    //LinkCodes
    const DEFAULT_LINK_CODE = "alb";
    const GEO_TARGETED_LINKS_DEFAULT_COUNTRY_LINK_CODE = "al0";
    const GEO_TARGETED_LINKS_REDIRECTED_COUNTRY_LINK_CODE = "al1";
    const MAXMIND_DATA_FILENAME = 'GeoLite2-Country.mmdb';

    //Cipher
    //Make a key of length 32 byte.
    //Specify your unique encryption key here.
    const ENCRYPTION_KEY = 'put your unique phrase here';
    //Make IV of 16 bytes
    const ENCRYPTION_IV = "0123456789ABCDEF";
    //Algorithm to use
    const ENCRYPTION_ALGORITHM = "aes-256-cbc";
    //Masking constant
    const AWS_SECRET_KEY_MASK = '••••••••••••••••••••••••••••••••••••••••';

    const SUCCESS = "SUCCESS";
    const FAIL = "FAIL";

    const UPLOADS_FOLDER = 'amazon-associates-link-builder/';

    //Search result items. Paapi returns 10 items by default.
    const MAX_SEARCH_RESULT_ITEMS = 9;

    //Shortcodes supported
    const SHORTCODE_AMAZON_LINK = 'amazon_link';
    const SHORTCODE_AMAZON_TEXT = 'amazon_textlink';

    //List of Default Amazon Template names
    const AMAZON_TEMPLATE_NAMES = 'ProductCarousel,ProductGrid,ProductAd,PriceLink,ProductLink';

    //Impression Recording Keys
    const IMPRESSION_RECORDER_SERVICE_KEY = 'impression_recorder_service';
    const ENDPOINT_KEY = 'endpoint';
    const ORG_UNIT_ID_KEY = 'org_unit_id';
}
