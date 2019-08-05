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
/*
 * Below is an example of context to be passed to the below template
 *
{
   "searchbox_placeholder":"Enter keyword(s)",
   "search_button_label":"Search",
   "associate_id_label":"Tracking IDs",
   "select_associate_id_label":"Select Tracking Id",
   "marketplace_label":"Marketplace",
   "select_marketplace_label":"Select Marketplace",
   "selected_products_list_label":"List of Selected Products(Maximum: 10)",
   "click_to_select_products_label":"Click to select product(s) to advertise",
   "text_shown_during_shortcode_creation":"Creating shortcode. Please wait....",
   "marketplace_help_content":"To configure marketplaces, go to Associates Link Builder plugin's Settings page",
   "tracking_id_help_content":"To configure tracking ids, go to Associates Link Builder plugin's Settings page",
   "searched_products_box_placeholder":Please select marketplace from above to show products.,
   "selected_products_box_placeholder":Please select some products from above.,
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
}
*/
?>
<script id="aalb-metabox-tab-hbs" type="text/x-handlebars-template">
    <div class="aalb-pop-up-container">
        <div class="aalb-admin-popup-shortcode-options aalb-table">
            <div class="aalb-table-row">
                <div class="aalb-table-cell">
                    <label title="{{marketplace_help_content}}">{{marketplace_label}}<i class="fa fa-info-circle aalb-info-icon" aria-hidden="true"></i></label>
                </div>
                <div class="aalb-table-cell">
                    <label title="{{tracking_id_help_content}}">{{associate_id_label}}<i class="fa fa-info-circle aalb-info-icon" aria-hidden="true"></i></label>
                </div>
                <div class="aalb-table-cell">
                    <label>{{search_keyword_label}}</label>
                </div>
            </div>

            <div class="aalb-table-row">
                <div class="aalb-admin-item-search-marketplaces aalb-width-25 aalb-table-cell">
                    <select class="aalb-marketplace-names-list" name="aalb-marketplace-names-list">
                        <option value="no-selection" disabled="disabled">{{select_marketplace_label}}</option>
                        {{#each marketplace_list}}
                        <option value="{{this}}" {{selected this ..
                        /default_marketplace}}>{{this}}</option>
                        {{/each}}
                    </select>
                </div>
                <div class="aalb-admin-popup-store  aalb-width-25 aalb-table-cell">
                    <select class="aalb-admin-popup-store-id" name="aalb-admin-popup-store-id">
                        <option value="no-selection" disabled="disabled">{{select_associate_id_label}}</option>
                        {{#each default_store_id_list}}
                        <option value="{{this}}" {{selected this ..
                        /default_store_id}}>{{this}}</option>
                        {{/each}}
                    </select>
                </div>

                <div class="aalb-width-40 aalb-table-cell">
                    <input type="text" class="aalb-admin-popup-input-search" name="aalb-admin-popup-input-search" placeholder="{{searchbox_placeholder}}" />
                </div>
                <div class="aalb-admin-searchbox aalb-width-10 aalb-table-cell">
                    <button class="aalb-btn aalb-btn-primary aalb-admin-popup-search-button" type="button">{{search_button_label}}</button>
                </div>
            </div>
        </div>
        <fieldset class="aalb-admin-popup-search-result aalb-admin-popup-fieldset">
            <legend class="aalb-admin-popup-legend">{{click_to_select_products_label}}</legend>
            <div class="aalb-admin-alert aalb-admin-alert-info aalb-admin-popup-placeholder">{{searched_products_box_placeholder}}</div>
        </fieldset>
        <fieldset class="aalb-selected aalb-admin-popup-fieldset">
            <legend class="aalb-admin-popup-legend">{{selected_products_list_label}}</legend>
            <div class="aalb-admin-alert aalb-admin-alert-info aalb-admin-popup-placeholder">{{selected_products_box_placeholder}}</div>
        </fieldset>
    </div>
</script>
