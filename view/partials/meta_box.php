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

namespace AmazonAssociatesLinkBuilder\admin\partials;
include AALB_ADMIN_ITEM_SEARCH_ITEMS_PATH;
include 'meta_box_tab_content.php';
include 'pop_up_content_hbs.php';

use AmazonAssociatesLinkBuilder\admin\Plugin_Admin;

/**
 * UI for Add short code popup. responsible to show list of products items based on the keywords.
 * This Metabox enables users to choose template, Associate ID and Market place for which product is being added based on the
 * details selected by plugin user short code is generated.
 */

// HandleBar template
$aalb_admin = new Plugin_Admin();

/*
 * Below is an example of context to be passed to the below template
 *
{
   "meta_box_tab_context":{
      "searchbox_placeholder":"Enter keyword(s)",
      "search_button_label":"Search",
      "associate_id_label":"Tracking IDs",
      "select_associate_id_label":"Select Tracking Id",
      "marketplace_label":"Marketplace",
      "select_marketplace_label":"Select Marketplace",
      "selected_products_list_label":"List of Selected Products(Maximum: 10)",
      "text_shown_during_shortcode_creation":"Creating shortcode. Please wait....",
      "marketplace_help_content":"To configure marketplaces, go to Associates Link Builder plugin's Settings page",
      "tracking_id_help_content":"To configure tracking ids, go to Associates Link Builder plugin's Settings page",
      "marketplace_list":[
         "IN",
         "BR",
         "IT",
         "CA",
         "US"
      ],
      "default_marketplace":"IN",
      "default_store_id_list":[
         "in-1",
         "in-2"
      ],
      "default_store_id":"in-1"
   },
   "add_shortcode_button_label":"Add Shortcode",
   "ad_template_label":"Ad Template",
   "templates_help_content":"To configure templates, go to Associates Link Builder plugin's Templates page",
   "templates_list":[
      "PriceLink",
      "ProductAd",
      "ProductCarousel",
      "ProductGrid",
      "ProductLink",
      "CopyOf-ProductAd",
      "CopyOf-ProductAds",
      "CopyOf-ProductCarousel",
      "ProductAdss",
      "ProductCarousel-width"
   ],
   "default_template":"ProductCarousel"
}
 */

?>
<!-- keeping css inline as css file does not load at plugin initialization  -->
<div id="aalb-admin-popup-container" style="display:none;">
    <div id="aalb-admin-pop-up">
        <script id="aalb-search-pop-up-hbs" type="text/x-handlebars-template">
            <div id="aalb-search-pop-up">
                <div class="aalb-admin-item-search-templates">
                    <label class="aalb-templates-label" title="{{templates_help_content}}">{{ad_template_label}}<i class="fa fa-info-circle aalb-info-icon" aria-hidden="true"></i></label>
                    <select id="aalb_template_names_list" name="aalb_template_names_list">
                        {{#each templates_list}}
                        <option value="{{this}}" {{selected this ..
                        /default_template}} {{this}}>{{this}}</option>
                        {{/each}}
                    </select>
                </div>

                <div id="aalb-tabs" class="aalb-pop-up-tabs">
                    <ul>
                        <li><a href="#aalb_tab1">{{meta_box_tab_context.default_marketplace}}</a></li>
                    </ul>

                    <div id="aalb_tab1">
                        {{> aalb-metabox-tab-hbs meta_box_tab_context}}
                    </div>
                </div>

                <div class="aalb-add-shortcode-button">
                    <button class="aalb-btn aalb-btn-primary" id="aalb-add-shortcode-button" type="button">{{add_shortcode_button_label}}</button>
                    <div id="aalb-add-shortcode-alert">
                        <div class="aalb-admin-icon"><i class="fa fa-spinner fa-pulse"></i></div>
                        {{text_shown_during_shortcode_creation}}
                    </div>
                    <div id="aalb-add-asin-error">
                        <div id="aalb-add-template-asin-error"></div>
                    </div>
                </div>
            </div>
        </script>
    </div>
</div>