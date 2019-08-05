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

namespace AmazonAssociatesLinkBuilder\includes;

use AmazonAssociatesLinkBuilder\constants\GB_Block_Constants;
use AmazonAssociatesLinkBuilder\shortcode\Shortcode_Loader;

/**
 * Class to manage plugin's gutenberg block.
 * @since      1.9.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder\includes
 */
class GB_Block_Manager
{
    private $shortcode_loader;

    const OPENING_SQUARE_BRACKET = '[';
    const CLOSING_SQUARE_BRACKET = ']';
    const TYPE_ARRAY = 'array';
    const ASIN = 'asin';
    const ASINS = 'asins';

    public function __construct()
    {
        $this->shortcode_loader = new Shortcode_Loader();
    }

    /**
     * Register Gutenberg block.
     */
    public function register_gb_block()
    {
        /**
         *  Check to confirm if Gutenberg is active.
         */
        if (!$this->is_gb_block_supported()) {
            // Gutenberg is not active.
            return false;
        }

        wp_register_script(
            GB_Block_Constants::GB_SCRIPT_HANDLE,
            AALB_GB_BLOCK_JS_URL,
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-i18n', 'wp-edit-post', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-api', 'wp-blocks'),
            filemtime(AALB_GB_BLOCK_JS_FILE)
        );

        register_block_type('amazon-associates-link-builder/aalb-gutenberg-block', array(
            'attributes' => array(
                GB_Block_Constants::SHORTCODE_ATTR => array(
                    'type' => GB_Block_Constants::SHORTCODE_ATTR_TYPE,
                ),
                GB_Block_Constants::SEARCH_KEYWORD => array(
                    'type' => GB_Block_Constants::SEARCH_KEYWORD_TYPE,
                )
            ),
            'editor_script' => 'amazon-associates-link-builder-gb-block',
            'render_callback' => function ($attributes) {
                $shortcode_val = $this->get_shortcode_value_from_attributes($attributes);
                return $this->is_valid_shortcode($shortcode_val) ? $this->get_render_output($shortcode_val) : null;
            }
        ));
    }

    /**
     * @return bool - returns whether Gutenberg editor supported or not.
     */
    public function is_gb_block_supported()
    {
        return function_exists(GB_Block_Constants::GB_SUPPORTED_IDENTIFIER_METHOD);
    }

    /**
     * Check if a shortcode is valid or not.
     * It validate by checking the following :-
     *  * If type of $shortcode  is array.
     *  * If $shortcode contains 'asin' or 'asins' keys.
     *
     * This method is used to check if shortcode attributes are added or not.
     *
     * @param $shortcode - shortcode.
     * @return bool
     */
    private function is_valid_shortcode($shortcode)
    {
        return (gettype($shortcode) == $this::TYPE_ARRAY) && (isset($shortcode[$this::ASIN]) || isset($shortcode[$this::ASINS]));
    }

    private function get_render_output($shortcode_val)
    {
        return $this->shortcode_loader->amazon_link_shortcode_callback($shortcode_val);
    }

    /**
     * @param $attributes
     * @return mixed
     */
    private function get_shortcode_value_from_attributes($attributes)
    {
        return isset($attributes[GB_Block_Constants::SHORTCODE_ATTR]) ? shortcode_parse_atts(trim(trim($attributes[GB_Block_Constants::SHORTCODE_ATTR], $this::OPENING_SQUARE_BRACKET), $this::CLOSING_SQUARE_BRACKET)) : $attributes;
    }
}

?>
