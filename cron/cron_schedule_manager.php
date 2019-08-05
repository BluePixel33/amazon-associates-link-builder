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

use AmazonAssociatesLinkBuilder\constants\Cron_Constants;
/**
 * Class which schedules and unschedules cron tasks
 *
 * @since      1.8.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/cron
 */
class Cron_Schedule_Manager {

    /**
     * Schedule cron tasks which update table and delete entries from the table
     *
     * @since 1.8.0
     */
    public function schedule_cron_tasks() {
        if ( ! wp_next_scheduled( Cron_Constants::UPDATE_TABLE_HOOK ) ) {
            wp_schedule_event( time(), Cron_Constants::UPDATE_TABLE_CRON_SCHEDULE_NAME, Cron_Constants::UPDATE_TABLE_HOOK );
        }
        if ( ! wp_next_scheduled( Cron_Constants::DELETE_FROM_TABLE_HOOK ) ) {
            wp_schedule_event( time(), Cron_Constants::DELETE_FROM_TABLE_CRON_SCHEDULE_NAME, Cron_Constants::DELETE_FROM_TABLE_HOOK );
        }
    }

    /**
     * Unschedule cron tasks which update table and delete entries from the table.
     *
     * @since 1.8.0
     */
    public function unschedule_cron_tasks() {
        if ( $timestamp = wp_next_scheduled( Cron_Constants::UPDATE_TABLE_HOOK ) ) {
            wp_unschedule_event( $timestamp, Cron_Constants::UPDATE_TABLE_HOOK );
        }
        if ( $timestamp = wp_next_scheduled( Cron_Constants::DELETE_FROM_TABLE_HOOK ) ) {
            wp_unschedule_event( $timestamp, Cron_Constants::DELETE_FROM_TABLE_HOOK );
        }
        if ( $timestamp = wp_next_scheduled( Cron_Constants::UPDATE_TABLE_CRON ) ) {
            wp_unschedule_event( $timestamp, Cron_Constants::UPDATE_TABLE_CRON );
        }
    }
}
