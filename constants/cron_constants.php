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
 * Class for Holding constants required for wp-cron.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class Cron_Constants {
    const BACKGROUND_PROCESSING_ACTION = 'update_table';
    const BACKGROUND_PROCESSING_PREFIX = 'aalb';
    const ASIN_KEY = 'asins';
    const MARKETPLACE_KEY = 'marketplace';
    const UPDATE_TABLE_HOOK = 'aalb_update_table_hook';
    const DELETE_FROM_TABLE_HOOK = 'aalb_delete_from_table_hook';
    const UPDATE_TABLE_CRON = 'aalb_update_table_cron';
    const UPDATE_TABLE_CRON_SCHEDULE_NAME = 'fifteen_minutes';
    /**
     * 15 minutes in seconds = 900.
     */
    const UPDATE_TABLE_CRON_SCHEDULE_DEFAULT_VALUE = 900;
    const DELETE_FROM_TABLE_CRON_SCHEDULE_NAME = 'six_hours';
    /**
     * 6 hours in seconds = 21600.
     */
    const DELETE_FROM_TABLE_CRON_SCHEDULE_VALUE = 21600;
    const CACHE_REFRESH_AGE = '25 MINUTE';
    const CACHE_EVICT_AGE = '24 HOUR';
    const DELAY_EXPONENT_ON_PAAPI_THROTTLE = 'aalb_delay_exponent_on_paapi_throttle';
    const DELAY_CONSTANT_VALUE = 100;
    const DELAY_BASE_VALUE = 2;
    const MAX_DELAY_LIMIT = 5000;
    const DELAY_EXPONENT_INITIAL_VALUE = 0;
}

?>
