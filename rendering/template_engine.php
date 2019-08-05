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
namespace AmazonAssociatesLinkBuilder\rendering;

use AmazonAssociatesLinkBuilder\constants\Db_Constants;
use AmazonAssociatesLinkBuilder\constants\Plugin_Constants;
use AmazonAssociatesLinkBuilder\helper\Plugin_Helper;

/**
 * Template engine to render the product in the particular display unit.
 *
 * @since      1.0.0
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/rendering
 */
class Template_Engine {
    protected $mustache;
    protected $helper;

    public function __construct() {
        $this->helper = new Plugin_Helper();
        $this->mustache = new \Mustache_Engine( array( 'loader' => new \Mustache_Loader_FilesystemLoader( AALB_TEMPLATE_DIR ) ) );
        $this->mustache_custom = new \Mustache_Engine( array( 'loader' => new \Mustache_Loader_FilesystemLoader( $this->helper->get_template_upload_directory() ) ) );
    }

    /**
     * Render the xml with a specific template.
     *
     * @since 1.0.0
     *
     * @param array $items     Each key consists of an item information object.
     * @param string $template Template in which the content has to be rendered.
     *
     * @return string HTML of the display unit.
     */
    public function render_xml( $items, $template ) {
        $aalb_default_templates = explode( ",", Plugin_Constants::AMAZON_TEMPLATE_NAMES );
        try {
            if ( in_array( $template, $aalb_default_templates ) ) {
                $template = $this->mustache->loadTemplate( $template );
            } else {
                $template = $this->mustache_custom->loadTemplate( $template );
            }
        } catch ( \Mustache_Exception_UnknownTemplateException $e ) {
            $template = $this->mustache->loadTemplate( get_option( Db_Constants::DEFAULT_TEMPLATE, Db_Constants::DEFAULT_TEMPLATE_NAME ) );
        }

        return $template->render( array( 'StoreId' => get_option( Db_Constants::DEFAULT_STORE_ID ), 'Items' => $items ) );
    }
}

?>