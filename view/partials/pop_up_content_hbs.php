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
   "text_shown_during_search":"Searching relevant products from Amazon",
   "check_more_on_amazon_text":"Check more search results on Amazon"
}
*/
?>

<script id="aalb-admin-pop-up-content-hbs" type="text/x-handlebars-template">
    <div class="aalb-admin-popup-content">
        <div class="aalb-admin-alert aalb-admin-alert-info aalb-admin-item-search-loading">
            <div class="aalb-admin-icon"><i class="fa fa-spinner fa-pulse"></i></div>
            {{text_shown_during_search}}
        </div>
        <div class="aalb-admin-item-search">
            <div class="aalb-admin-item-search-items"></div>
            <a href="#" target="_blank" class="aalb-admin-popup-more-results pull-right">{{check_more_on_amazon_text}}</a>
        </div>
    </div>
</script>