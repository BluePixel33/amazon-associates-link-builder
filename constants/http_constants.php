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
 * Class for Holding HTTP Constants
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class HTTP_Constants {
    //HTTP Status Codes
    const SUCCESS = '200';
    const BAD_REQUEST = '400';
    const REQUEST_URI_TOO_LONG = '414';
    const FORBIDDEN = '403';
    const INTERNAL_SERVER_ERROR = '500';
    const THROTTLE = '503';
    const TIME_OUT = '504';

    const CURL_ERROR_TIMEOUT_STRING = 'cURL error 28';
}