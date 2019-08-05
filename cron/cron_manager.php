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
use AmazonAssociatesLinkBuilder\constants\Cron_Constants;
use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\cron\Update_Table_Cron_Task;
use AmazonAssociatesLinkBuilder\sql\Sql_Helper;

/**
 * Class which manages all cron operations
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cron
 */
class Cron_Manager {

    private $update_table_cron_task;
    private $item_lookup_response_cache;

    public function __construct( Sql_Helper $sql_helper ) {
        $this->update_table_cron_task = new Update_Table_Cron_Task();
        $this->item_lookup_response_cache = new Item_Lookup_Response_Cache( $sql_helper );
    }

    /**
     * Get asins to update and add them in queue for background processing.
     *
     * @since 1.8.0
     */
    public function update_table() {
        $asins_list = $this->item_lookup_response_cache->get_asins_to_update();

        if ( sizeof( $asins_list ) == 0 ) {
            return;
        }

        $marketplace_asins_list_map = array();
        foreach ( $asins_list as $item ) {
            $marketplace_asins_list_map[$item->marketplace][] = $item->asin;
        }
        $marketplace_asins_chunks_map = array_map( array( $this, 'break_into_chunks' ), $marketplace_asins_list_map );

        foreach ( $marketplace_asins_chunks_map as $marketplace => $asins_chunk_array ) {
            foreach ( $asins_chunk_array as $asins_chunk ) {
                $queue_item = array( Cron_Constants::ASIN_KEY => $asins_chunk, Cron_Constants::MARKETPLACE_KEY => $marketplace );
                $this->update_table_cron_task->push_to_queue( $queue_item );
            }
        }

        $this->update_table_cron_task->save()->dispatch();
    }

    private function break_into_chunks( $item ) {
        return array_chunk( $item, 10 );
    }

    /**
     * Delete entries with last access time more than 24 hours from the table
     *
     * @since 1.8.0
     */
    public function delete_from_table() {
        $this->item_lookup_response_cache->delete_old_asins();
    }

    /**
     * Add cron intervals for cron tasks which update and delete the entries in the table.
     *
     * @since 1.8.0
     *
     * @param array $schedules Array containing cron schedules.
     *
     * @return array Modified schedules.
     */
    public function add_cron_intervals( $schedules ) {
        if ( ! array_key_exists( Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_NAME, $schedules ) ) {
            $schedules[Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_NAME] = array(
                'interval' => $this->get_cron_interval_value(),
                'display'  => esc_html__( Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_NAME ),
            );
        }
        if ( ! array_key_exists( Cron_Constants::DELETE_FROM_TABLE_CRON_SCHEDULE_NAME, $schedules ) ) {
            $schedules[Cron_Constants::DELETE_FROM_TABLE_CRON_SCHEDULE_NAME] = array(
                'interval' => Cron_Constants::DELETE_FROM_TABLE_CRON_SCHEDULE_VALUE,
                'display'  => esc_html__( Cron_Constants::DELETE_FROM_TABLE_CRON_SCHEDULE_NAME ),
            );
        }

        return $schedules;
    }

    /**
     * Get cron schedule value from wp_options if it is available, otherwise it returns default value.
     * The purpose of making it to read from wp_option is to make it testable.
     * @return int cron interval value.
     */
    private function get_cron_interval_value()
    {
        return get_option(Db_Constants::CRON_UPDATE_INTERVAL, Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_DEFAULT_VALUE);;
    }

}
