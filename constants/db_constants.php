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
 * Class for Holding Database key names. All key names have aalb_ as prefix to avoid name-conflict in common MySQL table
 * ToDo: Replace get_option by a db wrapper which also appends 'aalb_' to the key name so that we need not to maintain it here.
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class Db_Constants {
    const TEMPLATE_NAMES = 'aalb_template_names';
    const MARKETPLACE_NAMES = 'aalb_marketplace_names';
    const DEFAULT_TEMPLATE = 'aalb_default_template';
    const DEFAULT_STORE_ID = 'aalb_default_store_id';
    const DEFAULT_MARKETPLACE = 'aalb_default_marketplace';
    const AWS_ACCESS_KEY = 'aalb_aws_access_key';
    const AWS_SECRET_KEY = 'aalb_aws_secret_key';
    const CRED_CONFIG_GROUP = 'aalb_cred_config_group';
    const STORE_ID_NAMES = 'aalb_store_id_names';
    const STORE_IDS = 'aalb_store_ids';
    const CUSTOM_UPLOAD_PATH = 'aalb_custom_upload_path';
    const MAXMIND_DB_LAST_UPLOAD_PATH = 'aalb_maxmind_db_last_upload_path';
    const SHOW_HTTP_WARNING_ONCE = 'aalb_show_http_warning_once';
    const PLUGIN_VERSION = 'aalb_plugin_version';
    const NO_REFERRER_DISABLED = 'aalb_no_referrer_disabled';
    const GEOLITE_DB_DOWNLOAD_NEXT_RETRY_TIME = 'aalb_geolite_db_download_next_retry_time';
    const GEOLITE_DB_DOWNLOAD_RETRY_ON_FAILURE_DURATION = 'aalb_geolite_db_download_retry_on_failure_duration';
    const GEOLITE_DB_DOWNLOAD_FAILED_ATTEMPTS = 'aalb_geolite_db_download_failed_attempts';
    const ITEM_LOOKUP_RESPONSE_TABLE_NAME = 'Aalb_Asin_Response';
    const CRON_UPDATE_INTERVAL = 'aalb_update_table_cron_interval';

    //Defaults in case DB doesn't contain them.
    const DEFAULT_TEMPLATE_NAME = 'ProductCarousel';
    const DEFAULT_MARKETPLACE_NAME = 'US';
    const DEFAULT_STORE_ID_NAME = 'not-specified';
}

?>
