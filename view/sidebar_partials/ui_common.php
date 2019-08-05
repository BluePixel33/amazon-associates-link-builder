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

//ToDO: Move below functions into class.
//some commonly used UI functionality

function aalb_info_notice( $message ) {
    /* translators: %s: Information message */
    echo "<div class=\"notice notice-info is-dismissible\"><p>" . sprintf( esc_html__( "INFO - %s", 'amazon-associates-link-builder' ), $message ) . "</p></div>";
}

function aalb_warning_notice( $message ) {
    /* translators: %s: Warning message */
    echo "<div class=\"notice notice-warning is-dismissible\"><p>" . sprintf( esc_html__( "WARNING - %s", 'amazon-associates-link-builder' ), $message ) . "</p></div>";
}

function aalb_error_notice( $message ) {
    /* translators: %s: Error message */
    echo "<div class=\"notice notice-error is-dismissible\"><p>" . sprintf( esc_html__( "ERROR - %s", 'amazon-associates-link-builder' ), $message ) . "</p></div>";
}

function aalb_success_notice( $message ) {
    /* translators: %s: Success message */
    echo "<div class=\"notice notice-success is-dismissible\"><p>" . sprintf( esc_html__( "SUCCESS - %s", 'amazon-associates-link-builder' ), $message ) . "</p></div>";
}

?>
