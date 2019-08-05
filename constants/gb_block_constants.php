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

namespace AmazonAssociatesLinkBuilder\constants;

/**
 * Class for Holding constants required for Gutenberg editor.
 *
 * @since      1.9.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/constants
 */

class GB_Block_Constants
{
    const GB_SCRIPT_HANDLE = 'amazon-associates-link-builder-gb-block';
    const SHORTCODE_ATTR = 'shortCodeContent';
    const SHORTCODE_ATTR_TYPE = 'string';
    const SEARCH_KEYWORD = 'searchKeyword';
    const SEARCH_KEYWORD_TYPE = 'string';
    const GB_SUPPORTED_IDENTIFIER_METHOD = 'register_block_type';
}
?>
