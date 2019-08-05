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
?>

<script id="aalb-hbs-admin-items-search" type="text/x-handlebars-template">
    <div class="aalb-modal-box">
        {{#each this}}
        <div class="aalb-admin-item-search-items-item" data-asin="{{asin}}">
            <div class="aalb-admin-item-search-items-item-img">
                <img id="aalb-admin-item-search-items-item-img" src="{{image}}" />
            </div>
            <div class="aalb-admin-item-search-items-item-title">
                {{title}}
            </div>
            <div class="aalb-admin-item-search-items-item-price">
                {{price}}
            </div>
        </div>
        {{/each}}
    </div>
</script>