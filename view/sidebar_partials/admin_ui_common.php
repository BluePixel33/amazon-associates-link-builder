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

//File with common functionalities for Admin pages.
namespace AmazonAssociatesLinkBuilder\view\sidebar_partials;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;

include 'ui_common.php';

if ( ! is_admin() ) {
    die( "Permission Denied" );
}

if ( ! is_ssl() ) {
    //action when page is NOT using SSL
    if ( ! get_option( Db_Constants::SHOW_HTTP_WARNING_ONCE ) ) {
        // This info message is showed only once.
        aalb_info_notice( __( "We <b>recommend</b> using HTTPs connection for improved security.", 'amazon-associates-link-builder' ) );
        update_option( Db_Constants::SHOW_HTTP_WARNING_ONCE, true );
    }
}

?>
