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

namespace AmazonAssociatesLinkBuilder\cron;

use AmazonAssociatesLinkBuilder\cache\Item_Lookup_Response_Cache;
use AmazonAssociatesLinkBuilder\configuration\Config_Helper;
use AmazonAssociatesLinkBuilder\constants\Cron_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\HTTP_Constants;
use AmazonAssociatesLinkBuilder\helper\Paapi_Helper;
use AmazonAssociatesLinkBuilder\helper\Xml_Helper;
use AmazonAssociatesLinkBuilder\includes\Item_Lookup_Response_Manager;
use AmazonAssociatesLinkBuilder\rendering\Xml_Manipulator;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;

/**
 * Class which extends background processing and overrides the task function
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cron
 */
class Update_Table_Cron_Task extends \WP_Background_Process {

    private $paapi_helper;
    private $xml_manipulator;
    private $item_lookup_response_cache;
    private $item_lookup_response_manager;
    // Action string is used in WP_Background_Process's constructor, so it needs to be set here
    protected $prefix = Cron_Constants::BACKGROUND_PROCESSING_PREFIX;
    protected $action = Cron_Constants::BACKGROUND_PROCESSING_ACTION;

    public function __construct() {
        parent::__construct();
        $this->paapi_helper = new Paapi_Helper();
        $this->xml_manipulator = new Xml_Manipulator( new Xml_Helper( new Config_Helper() ) );
        $this->item_lookup_response_cache = new Item_Lookup_Response_Cache( new Sql_Helper( DB_NAME, Db_Constants::ITEM_LOOKUP_RESPONSE_TABLE_NAME ));
        $this->item_lookup_response_manager = new Item_Lookup_Response_Manager( $this->xml_manipulator );
    }

    /**
     * Task function which is used to process an item (array of group of asins separated by ",")
     *
     * @since 1.8.0
     *
     * @param array $queue_item Queue item to iterate over
     *
     * @return mixed False is returned if item is completely processed, else modified item is returned for further processing.
     */
    protected function task( $queue_item ) {
        if ( ! $queue_item ) {
            return false;
        }

        $asins_array = $queue_item[Cron_Constants::ASIN_KEY];
        $marketplace = $queue_item[Cron_Constants::MARKETPLACE_KEY];
        $store_id = $this->paapi_helper->get_store_id_for_marketplace( $marketplace );

        try {
            $items_array = $this->item_lookup_response_manager->get_response( $marketplace, $asins_array, $store_id );
            $this->item_lookup_response_cache->add( $items_array, $marketplace, false );
            delete_option( Cron_Constants::DELAY_EXPONENT_ON_PAAPI_THROTTLE );

            // If item is completely processed, remove the item from the queue
            return false;
        } catch ( \Exception $e ) {
            error_log( $this->paapi_helper->get_error_message( $e->getMessage() ) );

            if( $e->getMessage() === HTTP_Constants::THROTTLE ) {
                $this->exponential_backoff();
                return $queue_item;
            }
            else {
                // Remove item from Queue if there is any other exception (e.g. InvalidParameter etc).
                // The next cron will handle it. Or the item will get evicted after 24 hours from the cache.
                return false;
            }

        }
    }

    private function exponential_backoff(){
        if ( ! $delay_exponent = get_option( Cron_Constants::DELAY_EXPONENT_ON_PAAPI_THROTTLE ) ){
            $delay_exponent = Cron_Constants::DELAY_EXPONENT_INITIAL_VALUE;
        }
        $delay = pow( Cron_Constants::DELAY_BASE_VALUE,  $delay_exponent) * Cron_Constants::DELAY_CONSTANT_VALUE;
        if( $delay > Cron_Constants::MAX_DELAY_LIMIT ){
            $delay = Cron_Constants::MAX_DELAY_LIMIT;
        } else{
            $delay_exponent++;
        }
        update_option( Cron_Constants::DELAY_EXPONENT_ON_PAAPI_THROTTLE, $delay_exponent );
        usleep( $delay*1000 );
    }
}
