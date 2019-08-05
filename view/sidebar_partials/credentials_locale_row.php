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

namespace AmazonAssociatesLinkBuilder\view\sidebar_partials;

/*
 * Below is an example of context to be passed to this template
{
   "marketplace_list":[
      "IN",
      "US",
      "CN",
      "UK"
   ],
   "set_as_default_marketplace_label":"Set As Default Marketplace",
   "remove_marketplace_label":"Remove Marketplace",
   "default_marketplace_label":"Default Marketplace",
   "select_marketplace_label":"Select Marketplace",
   "tracking_id_placeholder":"Enter Tracking Id(s)",
   "default_marketplace_value":"IN",
   "marketplace":"IN",
   "tracking_ids":"store-1,store-2"
}
 */
?>

<script id="aalb-marketplace-row-hbs" type="text/x-handlebars-template">
    <tr class="aalb-marketplace-row">
        <th scope="row" class="aalb-settings-identifier">
            <select class="aalb-dropdown-marketplace">
                <option value="no-selection" disabled="disabled" selected="selected">{{select_marketplace_label}}</option>
                {{#each marketplace_list}}
                {{#ifCond this ../marketplace}}
                <option value={{this}} selected>{{this}}</option>
                {{else}}
                <option value={{this}}>{{this}}</option>
                {{/ifCond}}
                {{/each}}
            </select>
        </th>
        <td class="aalb-settings-input-column aalb-store-ids-column ">
            <input type="text" class="aalb-settings-input-field aalb-store-ids-for-marketplace" value="{{tracking_ids}}" placeholder="{{tracking_id_placeholder}}">
        </td>
        <td><a href="#" class="aalb-remove-marketplace">{{remove_marketplace_label}}</a></td>
        <td class="aalb-default-marketplace">
            {{#ifCond marketplace default_marketplace_value}}
            <span>{{default_marketplace_label}}</span>
            {{else}}
            <a href="#">{{set_as_default_marketplace_label}}</a>
            {{/ifCond}}
        </td>
    </tr>
</script>