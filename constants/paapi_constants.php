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
 * Class for Holding Paapi Constants
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class Paapi_Constants {
    const URI = '/onca/xml';
    const TRANSFER_PROTOCOL = 'https://';
    const URL_QUERY_SEPARATOR = '?';
    const SERVICE = 'AWSECommerceService';
    const VERSION = '2013-08-01';
    const INVALID_PARAMETER_VALUE_ERROR = 'AWS.InvalidParameterValue';

    // PAAPI Request Timeout in seconds
    const REQUEST_TIMEOUT = 35;

    const MARKETPLACES_URL = 'https://webservices.amazon.com/scratchpad/assets/config/config.json';
    const SIGN_UP_URL = 'http://docs.aws.amazon.com/AWSECommerceService/latest/DG/becomingDev.html';
    const MANAGE_US_ACCOUNT_URL = 'https://affiliate-program.amazon.com/gp/advertising/api/detail/your-account.html';
    const EFFICIENCY_GUIDELINES_URL = 'http://docs.aws.amazon.com/AWSECommerceService/latest/DG/TroubleshootingApplications.html#efficiency-guidelines';
}

?>