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

/**
 * Fired during Content rendering of the post. It Modifies contents of the blog post.
 * It is associated with the_content hook of the link builder plugin.
 *
 * @since      1.4.5
 * @package    AmazonAssociatesLinkBuilder
 * @subpackage AmazonAssociatesLinkBuilder/includes
 * @author     amipara
 */
class Content_Filter {
    private static $AMAZON_DOMAINS = array(
        'amazon\.com',
        'amazon\.fr',
        'amazon\.it',
        'amazon\.de',
        'amazon\.es',
        'amazon\.com\.br',
        'amazon\.ca',
        'amazon\.cn',
        'amazon\.in',
        'amazon\.co\.jp',
        'amazon\.com\.mx',
        'amazon\.co\.uk',
        'amazon\.com\.au',
        'amzn\.to'
    );

    /**
     * Attaches Content_Filter to the "the_content" hook of Wordpress
     * to intercept HTML content while post is being rendered if feature is enabled in settings.
     *
     * @since 1.4.5
     */
    static function attach() {
        if ( get_option( Db_Constants::NO_REFERRER_DISABLED ) ) {
            add_filter( 'the_content', array( new Content_Filter(), 'filter' ) );
        }
    }

    /**
     * Filters blog html content and does following.
     * 1. Removes "noreferrer" attribute from all amazon anchor links.
     *
     * @since 1.4.5
     *
     * @param string $content HTML blog content to be rendered.
     *
     * @return string Filtered HTML blog content
     */

    function filter( $content ) {
        /**
         *  To avoid "Warning: DOMDocument::loadHTML(): Empty string supplied as input".
         *  Warning is not shown if content contains only whitespaces or/and newlines.
         */
        if ( is_null( $content ) || $content === "" ) {
            return $content;
        }

        $document = new \DomDocument();

        //https://stackoverflow.com/questions/1148928/disable-warnings-when-loading-non-well-formed-html-by-domdocument-php
        //Disable warnings generated while parsing
        $libxml_previous_state = libxml_use_internal_errors( true );
        //Parse the Html Document
        $document->loadHTML( $content );
        //Clear all errors the were generated while parsing
        libxml_clear_errors();
        // Restore previous error queue before HTML parse
        libxml_use_internal_errors( $libxml_previous_state );

        $dom_xpath = new \DOMXPath( $document );
        $anchor_node_list = $dom_xpath->query( "//a[contains(@rel,'noreferrer')]" );

        $content_updated = false;

        //Remove noreferrer form amazon anchor links
        foreach ( $anchor_node_list as $anchor_node ) {
            if ( $this->has_amazon_link( $anchor_node ) ) {
                $this->remove_noreferrer( $anchor_node );
                $content_updated = true;
            }
        }

        return $content_updated ? $document->saveHTML() : $content;
    }

    /**
     * Checks if anchor node points to amazon url.
     * @link  https://stackoverflow.com/questions/3921066/php-regex-to-match-a-list-of-words-against-a-string
     *
     * @since 1.4.5
     *
     * @param \DOMElement $node HTML blog content to be rendered.
     *
     * @return boolean true when $node points to amazon url other wise false.
     */
    private function has_amazon_link( $node ) {
        $href_value = $node->getAttribute( 'href' );
        $parsed_url = parse_url( $href_value );

        return $parsed_url && array_key_exists( 'host', $parsed_url ) && preg_match_all( $this->amazon_url_regex(), $parsed_url['host'], $matches );
    }

    /**
     * Removes noreferrer attribute from $node.
     *
     * @since 1.4.5
     *
     * @param \DOMElement $node Anchor link node for which noreferrer needs to removed.
     *
     */
    private function remove_noreferrer( $node ) {
        $rel_value = $node->getAttribute( 'rel' );
        $rel_value = trim( str_replace( 'noreferrer', '', $rel_value ) );
        if ( $rel_value ) {
            $node->setAttribute( 'rel', $rel_value );
        } else {
            $node->removeAttribute( 'rel' );
        }
    }

    /**
     * Generates amazon url regex from list of amazon domain names.
     *
     * @since 1.4.5
     *
     * @return string amazon url regex
     */
    private function amazon_url_regex() {
        return '/(' . implode( '$|', Content_Filter::$AMAZON_DOMAINS ) . '$)/i';
    }

}