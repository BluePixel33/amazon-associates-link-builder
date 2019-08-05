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
namespace AmazonAssociatesLinkBuilder\view\partials;

use AmazonAssociatesLinkBuilder\admin\Plugin_Admin;

$aalb_settings_page_url = admin_url( 'admin.php?page=associates-link-builder-settings' );
$aalb_admin = new Plugin_Admin();
$aalb_admin->aalb_enqueue_styles();
$aalb_admin->aalb_enqueue_scripts();
?>

<!--
  UI for Search box shown in WordPress editors. User can type in keyword and trigger add short code box.
  Caution: Keep the onKeypress & onClick handlers inline and don't move them to aalb_admin.js. The reason for the same is that
  sometimes editor is dynamically created in some plugins and so these elements don't get bind to these events
-->

<div class="aalb-admin-inline aalb-admin-searchbox">
    <span class="aalb-admin-editor-tooltip aalb-admin-hide-display"></span>
    <img src=<?= AALB_ADMIN_ICON ?> class="aalb-admin-searchbox-amzlogo">
    <input type="text" class="aalb-admin-input-search" name="aalb-admin-input-search" placeholder="<?php esc_attr_e( "Enter keyword(s)", 'amazon-associates-link-builder' ) ?>" ,
        onkeypress="aalb_admin_object.editor_searchbox_keypress_event_handler( event, this )" />
    <a class="button aalb-admin-button-create-amazon-shortcode" title="<?php esc_attr_e( "Add Amazon Associates Link Builder Shortcode", 'amazon-associates-link-builder' ) ?>" ,
        onclick="aalb_admin_object.admin_show_create_shortcode_popup( this )"> <?php esc_html_e( "Search", 'amazon-associates-link-builder' ) ?>
    </a>
</div>